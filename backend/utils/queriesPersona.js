exports.obtenerIdComprador = `
    SELECT idpersona 
    FROM persona 
    WHERE nombre = ? AND tipo_persona = 'Cliente'
`;
