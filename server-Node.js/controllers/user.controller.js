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

module.exports = { getAllUsers, getUser }