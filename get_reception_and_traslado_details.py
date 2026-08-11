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

    target_receptions = (5763, 5772, 5790, 5791, 5819)

    print("--- DETAILED RECEPTION AND TRASLADO DATA ---")
    for rid in target_receptions:
        # Query reception details
        cursor.execute(f"SELECT * FROM logistica_recepciones WHERE idrecepcion = {rid}")
        rec = cursor.fetchone()
        if not rec:
            print(f"Reception {rid} not found.")
            continue
        
        # Query matching traslado details
        cursor.execute(f"SELECT * FROM logistica_traslados WHERE idtraslado = {rec['idtraslado']}")
        tras = cursor.fetchone()
        
        print(f"RECEPTION: {rid}")
        print(f"  - idtraslado: {rec['idtraslado']} | idenvio: {rec['idenvio']} | consecutivobodega: {rec['consecutivobodega']}")
        print(f"  - fecharecepcion: {rec['fecharecepcion']} | idbodega: {rec['idbodega']}")
        print(f"  - cantidadrecibida1 (cantidad): {rec['cantidadrecibida1']} | cantidadrecibida2 (cantidadsecundaria): {rec['cantidadrecibida2']}")
        print(f"  - folios: {rec['folios']}")
        
        if tras:
            print(f"TRASLADO: {rec['idtraslado']}")
            print(f"  - idfabricante (fabricante): {tras['idfabricante']} | idmarca (marca): {tras['idmarca']}")
            print(f"  - idproducto (producto): {tras['idproducto']} | idloteproducto (lote): {tras['idloteproducto']}")
            print(f"  - idestadoproducto (estado): {tras['idestadoproducto']}")
        else:
            print("  - Matching traslado not found.")
        print("-" * 80)

    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
