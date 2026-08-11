import pymysql
import json
import datetime

DB_HOST = "34.66.63.218"
DB_USER = "nmdevel"
DB_PASS = "nmdevel"
DB_NAME = "_dbmlog0000018677"

class DateTimeEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, (datetime.datetime, datetime.date)):
            return obj.isoformat()
        return super(DateTimeEncoder, self).default(obj)

def main():
    conn = pymysql.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME
    )
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    target_ids = (5763, 5772, 5790, 5791, 5819)

    print("--- 1. DETAILS FROM logistica_recepciones ---")
    sql = f"SELECT * FROM logistica_recepciones WHERE idrecepcion IN {target_ids}"
    cursor.execute(sql)
    recepciones = cursor.fetchall()
    for r in recepciones:
        print(f"ID Rec: {r['idrecepcion']} | Traslado: {r['idtraslado']} | Envio: {r['idenvio']} | Cons. Bodega: {r['consecutivobodega']} | Fecha: {r['fecharecepcion']} | Bodega: {r['idbodega']} | EstatusDoc: {r['idestadodocumento']} | Cant: {r['cantidadrecibida1']}")
    print()

    print("--- 2. CHECKING MATCHING MOVEMENT IN inventarios_movimientos ---")
    sql = f"SELECT * FROM inventarios_movimientos WHERE foliodoctoorigen IN {target_ids} AND doctoorigen = 4"
    cursor.execute(sql)
    movs = cursor.fetchall()
    print(f"Found {len(movs)} movements:")
    for m in movs:
        print(m)
    print()

    print("--- 3. CHECKING logistica_envios FOR THESE SHIPMENTS ---")
    envio_ids = tuple(r['idenvio'] for r in recepciones if r['idenvio'])
    if envio_ids:
        # Let's run a query to get shipment details
        sql = f"SELECT * FROM logistica_envios WHERE idenvio IN {envio_ids}"
        cursor.execute(sql)
        envios = cursor.fetchall()
        for e in envios:
            print(f"ID Envio: {e['idenvio']} | Traslado: {e['idtraslado']} | Cons. Bodega: {e['consecutivobodega']} | Fecha: {e['fechaenvio']} | EstatusDoc: {e['idestadodocumento']}")
    print()

    print("--- 4. CHECKING netwarelog_transacciones FOR THESE RECEPCIONS ---")
    try:
        sql_trans = "SELECT * FROM netwarelog_transacciones_2026_s2 WHERE sqlproceso LIKE %s OR sqlproceso LIKE %s OR sqlproceso LIKE %s OR sqlproceso LIKE %s OR sqlproceso LIKE %s ORDER BY fecha DESC"
        cursor.execute(sql_trans, [f"%{tid}%" for tid in target_ids])
        trans = cursor.fetchall()
        print(f"Found {len(trans)} transactions:")
        for t in trans:
            print(f"- {t['fecha']} | {t['usuario']} | {t['nombreproceso']} | IP: {t['ip']}\n  SQL: {t['sqlproceso']}")
    except Exception as e:
        print("Error reading netwarelog_transacciones:", e)
    print()

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
