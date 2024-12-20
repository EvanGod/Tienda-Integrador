const express = require('express');
const router = express.Router();
const purchaseController = require('../controllers/purchaseController');
const verifyToken = require('../middleware/authMiddleware'); // Middleware de verificación de token
const checkRole = require('../middleware/roleMiddleware'); // Middleware de verificación de rol

// Solo el rol 'Encargado' puede crear un ingreso (compra)
router.post('/ingreso', verifyToken, checkRole([2]), purchaseController.crearIngreso);

// Obtener los ingresos por día
router.get('/ingresos/dia', verifyToken, checkRole([1, 2]), purchaseController.obtenerIngresosPorDia);

// Obtener los ingresos por producto
router.get('/ingresos/producto', verifyToken, checkRole([1, 2]), purchaseController.obtenerIngresosPorProducto);

// Obtener el ID del usuario autenticado
router.get('/usuario', verifyToken, purchaseController.obtenerIdUsuario);

// Obtener el ID de un proveedor según su documento
router.post('/proveedor', verifyToken, checkRole([2]), purchaseController.obtenerIdProveedor);

module.exports = router;
