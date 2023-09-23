const express = require("express");
const app = express();
const connection = require("./configs/db.connection")

require("dotenv").config()
app.use(express.json())

const authRouter = require("./routes/auth.routes")
const authMiddleware = require("./middlewares/auth.middleware");
const userRouter = require("./routes/user.routes")

app.use("/", authRouter)
app.use("/user", authMiddleware, userRouter)

app.listen(8000, (err)=>{
    if(err){
        console.error(err)
        throw err
    }
    connection.connect((err) => {
          if (err) throw err
          console.log("Connected to DB")
        })
    console.log("server running on port: ", 8000)
})
