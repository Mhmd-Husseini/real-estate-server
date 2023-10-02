const express = require("express");
const router = express.Router();
const userController = require("../controllers/user.controller");

router.get("/", userController.getAllUsers)
router.get("/:id", userController.getUser)
router.post("/create", userController.createUser)
router.delete("/delete/:id", userController.deleteUser)
router.put("/update", userController.updateUser)

module.exports = router;