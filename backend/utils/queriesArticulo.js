// Obtener stock de un artículo
exports.obtenerStock = `
    SELECT stock FROM articulo WHERE idarticulo = ?
`;

// Actualizar stock de un artículo
exports.actualizarStock = `
    UPDATE articulo
    SET stock = stock - ?
    WHERE idarticulo = ? AND stock >= ?
`;

// Cambiar estado de un artículo a inactivo si su stock llega a 0
exports.cambiarEstadoSiSinStock = `
    UPDATE articulo
    SET estado = 0
    WHERE idarticulo = ? AND stock = 0
`;
