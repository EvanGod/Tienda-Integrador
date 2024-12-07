const express = require('express');
const router = express.Router();
const ventaController = require('../controllers/ventaController');
const verifyToken = require('../middleware/authMiddleware');
const checkRole = require('../middleware/roleMiddleware');

// Registrar una venta
router.post('/ventas', verifyToken, checkRole([3]), ventaController.registrarVenta);

// Obtener ventas por día
router.get('/ventas/por-dia', verifyToken, checkRole([1, 3]), ventaController.obtenerVentasPorDia);

// Obtener ventas por día
router.get('/obtener', verifyToken, checkRole([3, 1]), ventaController.obtenerTodasLasVentas);

// Obtener ventas por día
router.get('/ticket/:idventa', verifyToken, checkRole([3, 1]), ventaController.generarTicket);

// Obtener ventas por día
router.get('/obtener/:idventa', verifyToken, checkRole([3, 1]), ventaController.obtenerVentaPorId);

// Obtener ventas por día
router.get('/detalle/:idventa', verifyToken, checkRole([3, 1]), ventaController.obtenerDetalleVenta);

// Obtener ventas por producto
router.get('/ventas/por-producto', verifyToken, checkRole([1, 3]), ventaController.obtenerVentasPorProducto);

// Obtener ID de usuario
router.get('/ventas/id-usuario', verifyToken, checkRole([3, 1]), ventaController.obtenerIdUsuario);

// Obtener ID de comprador
router.get('/ventas/id-comprador', verifyToken, checkRole([3, 1]), ventaController.obtenerIdComprador);

module.exports = router;
