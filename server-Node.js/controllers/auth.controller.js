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

module.exports = { login, register }