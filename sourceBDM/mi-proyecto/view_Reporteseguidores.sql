CREATE VIEW Vista_SeguidoresConNombre AS
SELECT 
  s.SeguidoID,
  s.SeguidorID,
  u.NombreC AS nombre_seguidor,
  s.fecha_seguimiento
FROM Seguidores s
JOIN Usuarios u ON s.SeguidorID = u.ID;
