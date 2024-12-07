exports.insertarVenta = `
    INSERT INTO venta (idcliente, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha_hora, impuesto, total, estado) 
    VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, 'Activo')
`;

exports.ventasPorDia = `
    SELECT DATE(fecha_hora) AS fecha, COUNT(*) AS total_ventas, SUM(total) AS monto_total
    FROM venta
    GROUP BY DATE(fecha_hora)
    ORDER BY DATE(fecha_hora) DESC
`;

exports.ventasPorProducto = `
    SELECT a.nombre AS producto, SUM(dv.cantidad) AS total_vendido, SUM(dv.precio * dv.cantidad) AS ingresos_generados
    FROM detalle_venta dv
    JOIN articulo a ON dv.idarticulo = a.idarticulo
    GROUP BY a.idarticulo
    ORDER BY ingresos_generados DESC
`;

exports.obtenerComprobanteRepetido = `
    SELECT 1 
    FROM venta 
    WHERE num_comprobante = ?
    LIMIT 1
`;

exports.obtenerCliente = `
    SELECT 1 
    FROM persona 
    WHERE idpersona = ?
    LIMIT 1
`;

exports.obtenerStock = `
    SELECT stock 
    FROM articulo 
    WHERE idarticulo = ?
`;

exports.actualizarStock = `
    UPDATE articulo 
    SET stock = stock - ? 
    WHERE idarticulo = ?
`;

exports.insertarDetalleVenta = `
    INSERT INTO detalle_venta (idventa, idarticulo, cantidad, precio, descuento) 
    VALUES (?, ?, ?, ?, ?)
`;

exports.obtenerTodasLasVentas = `
    SELECT 
        v.idventa,
        p.nombre AS cliente,
        u.nombre AS usuario,
        v.tipo_comprobante,
        v.serie_comprobante,
        v.num_comprobante,
        v.fecha_hora,
        v.impuesto,
        v.total,
        v.estado
    FROM venta v
    JOIN persona p ON v.idcliente = p.idpersona
    JOIN usuario u ON v.idusuario = u.idusuario
    ORDER BY v.fecha_hora DESC;
`;

exports.obtenerVentaPorId = `
    SELECT 
        v.idventa,
        p.nombre AS cliente,
        u.nombre AS usuario,
        v.tipo_comprobante,
        v.serie_comprobante,
        v.num_comprobante,
        v.fecha_hora,
        v.impuesto,
        v.total,
        v.estado
    FROM venta v
    JOIN persona p ON v.idcliente = p.idpersona
    JOIN usuario u ON v.idusuario = u.idusuario
    WHERE v.idventa = ?;
`;
