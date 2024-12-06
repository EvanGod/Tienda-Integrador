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
