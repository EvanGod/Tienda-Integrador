const express = require('express');
const router = express.Router();
const { login, register } = require('../controllers/authController');
const checkRole = require('../middleware/roleMiddleware');
const verifyToken = require('../middleware/authMiddleware');

// Aquí debes asegurarte de pasar correctamente los middlewares y el controlador
router.post('/login', login);  // Login solo necesita el controlador

// La ruta register debe tener los middlewares en un arreglo
router.post('/register', [verifyToken, checkRole([1]), register]);

router.get('/admin-dashboard', verifyToken, checkRole([1]), (req, res) => {
  res.status(200).json({ message: 'Bienvenido al panel de administrador' });
});

router.get('/encargado-dashboard', verifyToken, checkRole([2]), (req, res) => {
  res.status(200).json({ message: 'Bienvenido al panel de encargado' });
});

router.get('/empleado-dashboard', verifyToken, checkRole([3]), (req, res) => {
  res.status(200).json({ message: 'Bienvenido al panel de empleado' });
});

module.exports = router;
