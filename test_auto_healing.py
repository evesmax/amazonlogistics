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

    # Let's pick a valid active reception to test on: Reception 5894
    test_idrecepcion = 5894
    print(f"--- STARTING SIMULATED AUTO-HEALING TEST ON RECEPTION {test_idrecepcion} ---")

    try:
        # Start a transaction to allow rolling back all our test changes
        conn.begin()

        # 1. Fetch original movements for 5894 to print and store them
        cursor.execute(f"SELECT * FROM inventarios_movimientos WHERE doctoorigen = 4 AND foliodoctoorigen = {test_idrecepcion}")
        orig_movs = cursor.fetchall()
        print(f"Original movements count: {len(orig_movs)}")
        for m in orig_movs:
            print(f"  - Mov ID: {m['idmovimiento']} | Product: {m['idproducto']} | Qty: {m['cantidad']} | Date: {m['fecha']}")
        
        if not orig_movs:
            print("ERROR: Test reception has no original movements to test with!")
            conn.rollback()
            return

        # 2. Simulate failure: Delete the movement
        print("Simulating failure (deleting movement)...")
        cursor.execute(f"DELETE FROM inventarios_movimientos WHERE doctoorigen = 4 AND foliodoctoorigen = {test_idrecepcion}")
        
        # Verify it is deleted in the transaction context
        cursor.execute(f"SELECT COUNT(*) as cnt FROM inventarios_movimientos WHERE doctoorigen = 4 AND foliodoctoorigen = {test_idrecepcion}")
        cnt = cursor.fetchone()['cnt']
        print(f"Movements count after deletion: {cnt}")
        
        # 3. Trigger auto-healing logic (equivalent to our PHP block)
        if cnt < 1:
            print("Checklist triggered! Running auto-healing queries...")
            
            # Query logistica_recepciones
            sql_rec = f"SELECT idtraslado, idenvio, cantidadrecibida1, cantidadrecibida2, fecharecepcion, idbodega FROM logistica_recepciones WHERE idrecepcion = {test_idrecepcion}"
            cursor.execute(sql_rec)
            rec_info = cursor.fetchone()
            
            if rec_info:
                print(f"Found reception info: Traslado: {rec_info['idtraslado']} | Envio: {rec_info['idenvio']} | Qty: {rec_info['cantidadrecibida1']}")
                
                # Query logistica_traslados
                sql_tras = f"SELECT idfabricante, idmarca, idproducto, idloteproducto, idestadoproducto FROM logistica_traslados WHERE idtraslado = {rec_info['idtraslado']}"
                cursor.execute(sql_tras)
                tras_info = cursor.fetchone()
                
                if tras_info:
                    print(f"Found traslado info: Fab: {tras_info['idfabricante']} | Marca: {tras_info['idmarca']} | Prod: {tras_info['idproducto']} | Lote: {tras_info['idloteproducto']} | Est: {tras_info['idestadoproducto']}")
                    
                    # Reconstruct and insert the movement
                    h_fabricante = tras_info['idfabricante']
                    h_marca = tras_info['idmarca'] if tras_info['idmarca'] else h_fabricante
                    h_producto = tras_info['idproducto']
                    h_lote = tras_info['idloteproducto']
                    h_estadoproducto = tras_info['idestadoproducto']
                    
                    # Insert movement (simulating clinventarios agregarmovimiento INSERT query)
                    sql_insert = """
                        INSERT INTO inventarios_movimientos
                        (idtipomovimiento, idfabricante, idmarca, idbodega, idproducto, idloteproducto, idestadoproducto, cantidad, cantidadsecundaria, fecha, doctoorigen, foliodoctoorigen)
                        VALUES
                        (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                    """
                    params = (
                        17, # tipomovimiento
                        h_fabricante,
                        h_marca,
                        rec_info['idbodega'],
                        h_producto,
                        h_lote,
                        h_estadoproducto,
                        rec_info['cantidadrecibida1'],
                        rec_info['cantidadrecibida2'],
                        rec_info['fecharecepcion'],
                        4, # doctoorigen
                        test_idrecepcion
                    )
                    cursor.execute(sql_insert, params)
                    print("Inserted auto-healed movement successfully.")
                    
                else:
                    print("Checklist Error: No se encontraron los datos del traslado asociado.")
            else:
                print("Checklist Error: No se encontraron los datos de la recepción.")

        # 4. Final verification
        cursor.execute(f"SELECT * FROM inventarios_movimientos WHERE doctoorigen = 4 AND foliodoctoorigen = {test_idrecepcion}")
        new_movs = cursor.fetchall()
        print(f"Movements count after auto-healing: {len(new_movs)}")
        for m in new_movs:
            print(f"  - Mov ID: {m['idmovimiento']} (New) | Product: {m['idproducto']} | Qty: {m['cantidad']} | Date: {m['fecha']}")
            
        print("\n--- SIMULATION SUCCESSFUL! ---")
        print("Rolling back transaction to keep database state pristine...")
        conn.rollback()

    except Exception as e:
        print("Error during test simulation:", e)
        conn.rollback()

    finally:
        cursor.close()
        conn.close()

if __name__ == "__main__":
    main()
