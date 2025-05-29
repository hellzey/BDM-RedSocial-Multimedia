DELIMITER //

CREATE FUNCTION fn_userLiked(publi INT, user INT) RETURNS BOOLEAN
BEGIN
  DECLARE liked BOOLEAN;
  SELECT EXISTS(SELECT 1 FROM Reacciones WHERE publiID = publi AND usuarioID = user AND tipo = 1) INTO liked;
  RETURN liked;
END //

DELIMITER ;
