const express = require('express');
const router = express.Router();
const { insertarProveedor, insertarComprador, obtenerProveedores, obtenerCompradores } = require('../controllers/personaController');
const checkRole = require('../middleware/roleMiddleware');
const verifyToken = require('../middleware/authMiddleware');
const validatePersona = require('../middleware/validatePersona');

// Ruta para insertar un proveedor (solo un Encargado puede hacerlo)
router.post('/insertar-proveedor', verifyToken, checkRole([2]),validatePersona, insertarProveedor);

// Ruta para insertar un comprador (un Empleado puede hacerlo)
router.post('/insertar-comprador', verifyToken, checkRole([3]),validatePersona, insertarComprador);

// Ruta para ver todos los proveedores (solo Encargados o Administradores pueden hacerlo)
router.get('/proveedores', verifyToken, checkRole([2, 1]), obtenerProveedores);

// Ruta para ver todos los compradores (solo Empleados o Administradores pueden hacerlo)
router.get('/compradores', verifyToken, checkRole([3, 1]), obtenerCompradores);

module.exports = router;
