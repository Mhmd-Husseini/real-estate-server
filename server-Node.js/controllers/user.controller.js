const connection = require('./../configs/db.connection.js');
const bcrypt = require("bcrypt");

const getAllUsers = async (req, res) => {
    connection.query("SELECT * FROM USERS WHERE role_id !=1", (err, result) => {
      if (err) console.log(err)
      res.send(result)
    });
  } 

module.exports = { getAllUsers }