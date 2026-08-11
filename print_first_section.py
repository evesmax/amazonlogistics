import pymysql
import json
import datetime

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

    target_ids = (5763, 5772, 5790, 5791, 5819)

    out = []

    sql = """
        SELECT fecha, usuario, nombreproceso, sqlproceso, ip 
        FROM netwarelog_transacciones_2026_s2 
        WHERE fecha >= '2026-08-01 00:00:00'
          AND (sqlproceso LIKE '%5763%' OR sqlproceso LIKE '%5772%' OR sqlproceso LIKE '%5790%' OR sqlproceso LIKE '%5791%' OR sqlproceso LIKE '%5819%')
        ORDER BY fecha DESC
    """
    cursor.execute(sql)
    trans = cursor.fetchall()
    
    out.append("==================================================================")
    out.append(f"TRANSACTIONS MATCHING TARGET IDs: {len(trans)}")
    out.append("==================================================================")
    for t in trans:
        out.append(f"FECHA: {t['fecha']} | USUARIO: {t['usuario']} | PROCESO: {t['nombreproceso']} | IP: {t['ip']}")
        out.append(f"SQL: {t['sqlproceso']}")
        out.append("-" * 80)
        
    out.append("\n\n")
    
    # Also let's query the specific actions on logistica_recepciones for these IDs
    sql_rec = f"SELECT idrecepcion, consecutivobodega, fecharecepcion, idbodega, folios, referencia, idestadodocumento FROM logistica_recepciones WHERE idrecepcion IN {target_ids}"
    cursor.execute(sql_rec)
    recs = cursor.fetchall()
    out.append("==================================================================")
    out.append(f"logistica_recepciones ROWS: {len(recs)}")
    out.append("==================================================================")
    for r in recs:
        out.append(str(r))
        out.append("-" * 80)

    with open("results.txt", "w", encoding="utf-8") as f:
        f.write("\n".join(out))

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
