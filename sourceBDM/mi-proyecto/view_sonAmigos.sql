CREATE OR REPLACE VIEW VistaAmigos AS
SELECT
    LEAST(s1.SeguidorID, s1.SeguidoID) AS Usuario1,
    GREATEST(s1.SeguidorID, s1.SeguidoID) AS Usuario2
FROM Seguidores s1
JOIN Seguidores s2
    ON s1.SeguidorID = s2.SeguidoID AND s1.SeguidoID = s2.SeguidorID
WHERE s1.SeguidorID < s1.SeguidoID
GROUP BY Usuario1, Usuario2;
