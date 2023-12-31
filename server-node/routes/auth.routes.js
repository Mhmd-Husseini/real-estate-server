const express = require("express");
const router = express.Router();
const authController = require("../controllers/auth.controller");

router.post("/", authController.login)
router.post("/register", authController.register)
router.get("/profile", authController.getProfile)
router.put("/updateAdmin", authController.updateAdmin)

module.exports = router;