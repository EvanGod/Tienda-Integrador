const express = require('express');
const router = express.Router();
const productoController = require('../controllers/productoController');
const verifyToken = require('../middleware/authMiddleware');  // Verificación del token JWT
const checkRole = require('../middleware/roleMiddleware');  // Verificación de roles

// Solo los roles 'Encargado' y 'Administrador' pueden crear productos
router.post('/productos', verifyToken, checkRole(['Encargado', 'Administrador']), productoController.crearProducto);

// Solo los roles 'Encargado' y 'Administrador' pueden actualizar productos
router.put('/productos/:idarticulo', verifyToken, checkRole(['Encargado', 'Administrador']), productoController.actualizarProducto);

// Solo los roles 'Encargado' y 'Administrador' pueden eliminar productos
router.delete('/productos/:idarticulo', verifyToken, checkRole(['Encargado', 'Administrador']), productoController.eliminarProducto);

// Los productos pueden ser leídos por cualquier usuario autenticado
router.get('/productos', verifyToken, productoController.obtenerProductos);

// Obtener producto por ID (cualquier usuario autenticado)
router.get('/productos/:idarticulo', verifyToken, productoController.obtenerProductoPorId);

module.exports = router;
