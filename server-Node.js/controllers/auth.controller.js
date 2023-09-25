const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const connection = require('./../configs/db.connection.js');

const login = (req, res) => {
    const { email, password } = req.body;
    if (!email || !password) res.status(401).send({ message: "Email and Password are required" })
    connection.query("SELECT * FROM USERS WHERE email = ? AND role_id = 1", [email], async (err, result) => {
      if (err) return res.status(500).send({ message: "Something wrong happened" })
      if (result.length !== 1) return res.status(401).send({ message: " Incorrect Email/Password" })
      const user = result[0];
      const isValid = await bcrypt.compare(password, user.password);
      if (!isValid) return res.status(401).send({ message: "Email and Password are required" })
      const { password: hashedPassword, ...userInfo } = user
      const token = jwt.sign(userInfo, process.env.JWT_SECRET)
      return res.send({
        token,
        user: userInfo
      })
    })
  }

  const register = async (req, res) => {
    const { name, email, password, phone } = req.body;
    
    if (!name || !email || !password || !phone) {
      return res.status(400).send({ message: 'All fields are required' });
    }
  
    try {
      const emailExists = await new Promise((resolve, reject) => {
        connection.query('SELECT * FROM USERS WHERE email = ?', [email], (err, result) => {
          if (err) {
            reject(err);
          } else {
            resolve(result.length > 0);
          }
        });
      });
  
      if (emailExists) {
        return res.status(409).send({ message: 'Email already registered' });
      }
  
      const hashedPassword = await bcrypt.hash(password, 10);
  
      connection.query(
        'INSERT INTO USERS (name, email, password, phone, role_id) VALUES (?, ?, ?, ?, 1)',
        [name, email, hashedPassword, phone, 1],
        (err, result) => {
          if (err) {
            console.error(err);
            return res.status(500).send({ message: 'Failed to register user' });
          }
  
          const userInfo = { name, email, phone, role_id: 1 }; 
          const token = jwt.sign(userInfo, process.env.JWT_SECRET);
  
          return res.status(201).send({ token, user: userInfo });
        }
      );
    } catch (error) {
      console.error(error);
      return res.status(500).send({ message: 'Something went wrong' });
    }
  };

  const getProfile = async (req, res) => {
    const token = req.header('Authorization');

    if (!token) {
      return res.status(401).send({ message: 'Authorization token not provided' });
    }
    
    try {
      const tokenWithoutBearer = token.replace('Bearer ', '');
      const decoded = jwt.verify(tokenWithoutBearer, process.env.JWT_SECRET);
      const userId = decoded.id;
    
      connection.query(
        'SELECT * FROM USERS WHERE id = ?',
        [userId],
        (err, result) => {
          if (err) {
            console.log(err);
            return res.status(500).send({ message: 'Internal Server Error' });
          }
    
          if (result.length === 0) {
            return res.status(404).send({ message: 'User not found' });
          }
    
          const userWithoutPassword = {
            id: result[0].id,
            name: result[0].name,
            email: result[0].email,
            phone: result[0].phone,
          };
      
          res.send(userWithoutPassword);
        }        
      );
    } catch (error) {
      console.log(error);
      res.status(401).send({ message: 'Invalid token', token: token });
    }
  }
  
  const updateAdmin = async (req, res) => {
    const token = req.header('Authorization');
  
    if (!token) {
      return res.status(401).json({ error: 'Authorization token not provided' });
    }
  
    try {
      const tokenWithoutBearer = token.replace('Bearer ', '');
      const decoded = jwt.verify(tokenWithoutBearer, process.env.JWT_SECRET);
      const userId = decoded.id;
  
      const { password, name, phone, email } = req.body;
      const hashedPassword = password ? await bcrypt.hash(password, 10) : null;
      const updateFields = [];
      const queryParams = [];
  
      if (name) {
        updateFields.push('name = ?');
        queryParams.push(name);
      }
      if (email) {
        updateFields.push('email = ?');
        queryParams.push(email);
      }
      if (phone) {
        updateFields.push('phone = ?');
        queryParams.push(phone);
      }
      if (hashedPassword) {
        updateFields.push('password = ?');
        queryParams.push(hashedPassword);
      }
  
      if (updateFields.length === 0) {
        return res.status(400).json({ error: 'No fields to update' });
      }
  
      connection.query(
        'SELECT * FROM USERS WHERE id = ?',[userId],(err, userResult) => {
          if (err) {
            console.error(err);
            return res.status(500).json({ error: 'Failed to check user existence' });
          }
  
          if (userResult.length === 0) {
            return res.status(404).json({ error: 'User not found' });
          }
  
          queryParams.push(userId);
          const sql = `UPDATE USERS SET ${updateFields.join(', ')} WHERE id = ?`;
          connection.query(sql, queryParams, (updateErr, result) => {
            if (updateErr) {
              console.error(updateErr);
              return res.status(500).json({ error: 'Failed to update user' });
            }
            if (result.affectedRows > 0) {
              connection.query(
                'SELECT * FROM USERS WHERE id = ?',
                [userId],
                (fetchErr, userResult) => {
                  if (fetchErr) {
                    console.error(fetchErr);
                    return res.status(500).json({ error: 'Failed to fetch updated user data' });
                  }
                  res.status(200).json({ status: 'success', user: userResult[0] });
                }
              );
            } else {
              res.status(404).json({ error: 'User not found' });
            }
          });
        }
      );
    } catch (error) {
      console.error(error);
      res.status(401).json({ error: 'Invalid token', token: token });
    }
  };
  
module.exports = { login, register, getProfile, updateAdmin }