CREATE OR REPLACE VIEW vw_PublicacionesUsuarios AS
SELECT 
  p.publiID,
  p.descripcion,
  p.fechacreacion,
  p.usuarioID,
  u.Nick,
  u.Foto,
  u.NombreC,
  p.likes_count 
FROM Publicaciones p
JOIN Usuarios u ON p.usuarioID = u.ID;
