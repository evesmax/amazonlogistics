import pymysql

# Database connection credentials
DB_HOST = "34.66.63.218"
DB_USER = "nmdevel"
DB_PASS = "nmdevel"
DB_NAME = "_dbmlog0000018677"

def main():
    try:
        conn = pymysql.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASS,
            database=DB_NAME
        )
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        print("================================================================")
        print("          SCM DATABASE INTEGRITY DIAGNOSTIC TOOL")
        print("================================================================\n")

        # --- 1. RECEPCIONES SIN MOVIMIENTO DE INVENTARIO ---
        print("[1] Checking for active receptions with missing inventory movements...")
        # doctoorigen = 4 is standard/direct receptions
        sql_missing_mov = """
            SELECT r.idrecepcion, r.idtraslado, r.idenvio, r.fecharecepcion, r.referencia, r.idestadodocumento
            FROM logistica_recepciones r
            LEFT JOIN inventarios_movimientos m 
              ON m.foliodoctoorigen = r.idrecepcion AND m.doctoorigen = 4
            WHERE r.idestadodocumento = 1 AND m.idmovimiento IS NULL
            ORDER BY r.idrecepcion DESC
        """
        cursor.execute(sql_missing_mov)
        rows = cursor.fetchall()
        if rows:
            print(f"  ❌ FOUND {len(rows)} active receptions without inventory movements:")
            for r in rows:
                print(f"     - idrecepcion: {r['idrecepcion']} | traslado: {r['idtraslado']} | envio: {r['idenvio']} | fecha: {r['fecharecepcion']} | ref: {r['referencia']}")
        else:
            print("  ✅ All active receptions have corresponding inventory movements.")
        print()

        # --- 2. RECEPCIONES HUÉRFANAS (SIN ENVÍO ASOCIADO EN ENVÍOS) ---
        print("[2] Checking for receptions pointing to non-existent shipments...")
        sql_orphan_rec = """
            SELECT r.idrecepcion, r.idenvio, r.idtraslado, r.fecharecepcion
            FROM logistica_recepciones r
            LEFT JOIN logistica_envios e ON e.idenvio = r.idenvio
            WHERE r.idenvio IS NOT NULL AND r.idenvio != 0 AND e.idenvio IS NULL
            ORDER BY r.idrecepcion DESC
        """
        cursor.execute(sql_orphan_rec)
        rows = cursor.fetchall()
        if rows:
            print(f"  ❌ FOUND {len(rows)} orphan receptions pointing to non-existent shipments:")
            for r in rows:
                print(f"     - idrecepcion: {r['idrecepcion']} | idenvio (inexistente): {r['idenvio']} | traslado: {r['idtraslado']} | fecha: {r['fecharecepcion']}")
        else:
            print("  ✅ All receptions match existing shipments.")
        print()

        # --- 3. ENVÍOS HUÉRFANAS (SIN RECEPCIÓN ASOCIADA EN RECEPCIONES) ---
        print("[3] Checking for shipments pointing to non-existent receptions...")
        sql_orphan_env = """
            SELECT e.idenvio, e.idrecepcion, e.idtraslado, e.fechaenvio
            FROM logistica_envios e
            LEFT JOIN logistica_recepciones r ON r.idrecepcion = e.idrecepcion
            WHERE e.idrecepcion IS NOT NULL AND e.idrecepcion != 0 AND r.idrecepcion IS NULL
            ORDER BY e.idenvio DESC
        """
        cursor.execute(sql_orphan_env)
        rows = cursor.fetchall()
        if rows:
            print(f"  ❌ FOUND {len(rows)} orphan shipments pointing to non-existent receptions:")
            for r in rows:
                print(f"     - idenvio: {r['idenvio']} | idrecepcion (inexistente): {r['idrecepcion']} | traslado: {r['idtraslado']} | fecha: {r['fechaenvio']}")
        else:
            print("  ✅ All shipments match existing receptions.")
        print()

        # --- 4. INCONSISTENCIAS DE ESTATUS (ENVÍO RECIBIDO PERO SIN ESTATUS 3) ---
        print("[4] Checking for status mismatches (shipments received but not in status 3)...")
        sql_status_mismatch = """
            SELECT e.idenvio, e.idrecepcion, e.idestadodocumento as envio_status, r.idestadodocumento as rec_status
            FROM logistica_envios e
            INNER JOIN logistica_recepciones r ON r.idrecepcion = e.idrecepcion
            WHERE r.idestadodocumento = 1 AND e.idestadodocumento != 3
            ORDER BY e.idenvio DESC
        """
        cursor.execute(sql_status_mismatch)
        rows = cursor.fetchall()
        if rows:
            print(f"  ❌ FOUND {len(rows)} shipments with status mismatches:")
            for r in rows:
                print(f"     - idenvio: {r['idenvio']} (status: {r['envio_status']}) | idrecepcion: {r['idrecepcion']} (status: {r['rec_status']})")
        else:
            print("  ✅ All received shipments have the correct status (3).")
        print()

        cursor.close()
        conn.close()
        print("================================================================")
        print("                       DIAGNOSTIC COMPLETE")
        print("================================================================")

    except Exception as e:
        print("Error connecting or executing diagnostic queries:", e)

if __name__ == "__main__":
    main()
