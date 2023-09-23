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

  module.exports = { login }
