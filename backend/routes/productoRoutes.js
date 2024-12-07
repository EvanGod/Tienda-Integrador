const express = require('express');
const router = express.Router();
const productoController = require('../controllers/productoController');
const verifyToken = require('../middleware/authMiddleware');  // Verificación del token JWT
const checkRole = require('../middleware/roleMiddleware');  // Verificación de roles
const { validateProduct, updateProduct } = require('../middleware/validateProducto');




// Solo los roles 2 y 3 pueden crear productos
router.post('/productos', verifyToken, checkRole([2, 3]), validateProduct, productoController.crearProducto);

// Solo los roles 2 y 3 pueden actualizar productos
router.put('/productos/:idarticulo', verifyToken, checkRole([2, 3]),updateProduct,  productoController.actualizarProducto);

// Solo los roles 2 y 3 pueden eliminar productos
router.delete('/productos/:idarticulo', verifyToken, checkRole([2, 3]), productoController.eliminarProducto);

// Los productos pueden ser leídos por cualquier usuario autenticado
router.get('/productos', verifyToken, productoController.obtenerProductos);

// Los productos pueden ser leídos por cualquier usuario autenticado
router.get('/prod', verifyToken, productoController.obtenerProductosVenta);

// Obtener producto por ID (cualquier usuario autenticado)
router.get('/productos/:idarticulo', verifyToken, productoController.obtenerProductoPorId);

module.exports = router;
