const express = require('express');
const router = express.Router();
const ventaController = require('../controllers/ventaController');
const verifyToken = require('../middleware/authMiddleware');
const checkRole = require('../middleware/roleMiddleware');

// Registrar una venta
router.post('/ventas', verifyToken, checkRole(['Empleado', 'Administrador']), ventaController.registrarVenta);

// Obtener ventas por día
router.get('/ventas/por-dia', verifyToken, checkRole(['Administrador']), ventaController.obtenerVentasPorDia);

// Obtener ventas por producto
router.get('/ventas/por-producto', verifyToken, checkRole(['Administrador']), ventaController.obtenerVentasPorProducto);

// Obtener ID de usuario
router.get('/ventas/id-usuario', verifyToken, checkRole(['Empleado', 'Administrador']), ventaController.obtenerIdUsuario);

// Obtener ID de comprador
router.get('/ventas/id-comprador', verifyToken, checkRole(['Empleado', 'Administrador']), ventaController.obtenerIdComprador);

module.exports = router;
