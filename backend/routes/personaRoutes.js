const express = require('express');
const router = express.Router();
const { insertarProveedor, insertarComprador, obtenerProveedores, obtenerCompradores } = require('../controllers/personaController');
const checkRole = require('../middleware/roleMiddleware');
const verifyToken = require('../middleware/authMiddleware');

// Ruta para insertar un proveedor (solo un Encargado puede hacerlo)
router.post('/insertar-proveedor', verifyToken, checkRole(['Encargado']), insertarProveedor);

// Ruta para insertar un comprador (un Empleado puede hacerlo)
router.post('/insertar-comprador', verifyToken, checkRole(['Empleado']), insertarComprador);

// Ruta para ver todos los proveedores (solo Encargados o Administradores pueden hacerlo)
router.get('/proveedores', verifyToken, checkRole(['Encargado', 'Administrador']), obtenerProveedores);

// Ruta para ver todos los compradores (solo Empleados o Administradores pueden hacerlo)
router.get('/compradores', verifyToken, checkRole(['Empleado', 'Administrador']), obtenerCompradores);

module.exports = router;
