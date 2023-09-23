const connection = require('./../configs/db.connection.js');

const hello = async (req, res) => {
    res.send({ message: "hello" });
  };  

module.exports = { hello }