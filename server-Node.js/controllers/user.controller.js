const connection = require('./../configs/db.connection.js');
const bcrypt = require("bcrypt");

const getAllUsers = async (req, res) => {
    connection.query("SELECT * FROM USERS WHERE role_id !=1", (err, result) => {
      if (err) console.log(err)
      res.send(result)
    });
  } 

  const getUser = async (req, res) => {
    const { id } = req.params;
    console.log(id)
    connection.query(`SELECT * FROM USERS WHERE id = ?`, [id], (err, result) => {
      if (err) console.log(err)
      if (result.length === 0) res.status(404).send({ message: "User not found" })
      res.send(result[0])
    })
  }

  const createUser = async (req, res) => {
    const { role_id, password, name, phone, email } = req.body
    const hashedPassword = await bcrypt.hash(password, 10);
    connection.query("INSERT INTO USERS (role_id, name, phone, email, password) VALUES (?, ?, ?, ?, ?)", [role_id, name, phone, email, hashedPassword], (err, result) => {
      if (err) console.log(err)
      if (result.insertId) {
        connection.query(`SELECT * FROM USERS WHERE id = ?`, [result.insertId], (err, result) => {
          if (err) console.log(err)
          res.send(result[0])
        })
      }
    })
  }

  const deleteUser = (req, res) => {
    const { id } = req.params;
    const sql = `DELETE FROM users WHERE id = ?`;
    connection.query(sql, [id], (err, result) => {
      if (err) {
        console.error('Error deleting user:', err);
        res.status(500).json({ error: 'An error occurred while deleting the user.' });
      } else {
        if (result.affectedRows > 0) {
          res.status(200).json({ message: 'User deleted successfully.' });
        } else {
          res.status(404).json({ error: 'User not found.' });
        }
      }
    });
  };

  const updateUser = async (req, res) => {
    const { id, password, name, phone, email } = req.body;
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
    
    connection.query('SELECT * FROM USERS WHERE id = ?', [id], (err, userResult) => {
      if (err) {
        console.error(err);
        return res.status(500).json({ error: 'Failed to check user existence' });
      }

      if (userResult.length === 0) {
        return res.status(404).json({ error: 'User not found' });
      }

      queryParams.push(id);
      const sql = `UPDATE USERS SET ${updateFields.join(', ')} WHERE id = ?`;
      connection.query(sql, queryParams, (updateErr, result) => {
        if (updateErr) {
          console.error(updateErr);
          return res.status(500).json({ error: 'Failed to update user' });
        }
        if (result.affectedRows > 0) {
          connection.query('SELECT * FROM USERS WHERE id = ?', [id], (fetchErr, userResult) => {
            if (fetchErr) {
              console.error(fetchErr);
              return res.status(500).json({ error: 'Failed to fetch updated user data' });
            }
            res.send(userResult[0]);
          });
        } else {
          res.status(404).json({ error: 'User not found' });
        }
      });
    });
  }

module.exports = { getAllUsers, getUser, createUser, deleteUser, updateUser }