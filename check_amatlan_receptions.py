import pymysql

DB_HOST = "34.66.63.218"
DB_USER = "nmdevel"
DB_PASS = "nmdevel"
DB_NAME = "_dbmlog0000018677"

def main():
    conn = pymysql.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME
    )
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    # We need to find all active receptions (idestadodocumento = 1) for Amatlán (idbodega = 1)
    # matching the product and lot.
    sql = """
        SELECT lr.idrecepcion, lr.idtraslado, lr.idenvio, lr.cantidadrecibida1, lr.cantidadrecibida2, lr.fecharecepcion
        FROM logistica_recepciones lr
        INNER JOIN logistica_traslados lt ON lr.idtraslado = lt.idtraslado
        WHERE lr.idbodega = 1
          AND lt.idfabricante = 20
          AND lt.idmarca = 20
          AND lt.idproducto = 56
          AND lt.idloteproducto = 16
          AND lt.idestadoproducto = 4
          AND lr.idestadodocumento = 1
        ORDER BY lr.idrecepcion ASC
    """
    cursor.execute(sql)
    receptions = cursor.fetchall()
    print("--- ACTIVE RECEPTIONS IN logistica_recepciones ---")
    sum_rec1 = 0.0
    sum_rec2 = 0.0
    for r in receptions:
        print(f"Rec: {r['idrecepcion']} | Envio: {r['idenvio']} | Cant1: {r['cantidadrecibida1']} | Cant2: {r['cantidadrecibida2']} | Fecha: {r['fecharecepcion']}")
        sum_rec1 += float(r['cantidadrecibida1'])
        sum_rec2 += float(r['cantidadrecibida2'])
    
    print(f"Total quantity in logistica_recepciones: {sum_rec1}")
    print(f"Total secondary quantity in logistica_recepciones: {sum_rec2}")

    print("\n--- COMPARISON ---")
    print(f"Receptions Sum: {sum_rec1} | Movements Sum: 21798.0 | Saldos: 21802.0")

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
