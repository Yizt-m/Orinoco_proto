import sys
import pandas as pd
import numpy as np
import json

def procesar_reportes(ruta_at48, ruta_d01):
    try:
        # --- 1. Cargar Archivo AT48 ---
        at48_headers = [
            'Tipo Persona RIF', 'Identificación Tipo Persona RIF', 'Nombre del Cliente', 
            'Condicón de Residencia', 'Actividad Economica Cliente', 'Fecha de Operación', 
            'Tipo Operación Cambiaria', 'Movimiento de la Transacción', 'Tipo de Operación Divisa', 
            'Moneda', 'Monto Divisa', 'Tipo Cambio', 'Contravalor en Bs', 
            'Pais destino de la Transferencia', 'Destino de los Fondos', 'Medio de Pago', 
            'Porcentaje de Comisión', 'Monto de Comisión (Bs)', 'Tipo de Cambio Adquisición', 
            'Tipo de Cambio Según Libros', 'Ganancia y/o Pérdidas en Bs', 'Referencia de la Operación'
        ]
        df_at48 = pd.read_csv(ruta_at48, sep='~', header=None, names=at48_headers, dtype=str)

        # --- 2. Cargar Archivo D01 ---
        df_d01 = pd.read_csv(ruta_d01, sep=',', dtype=str)
        df_d01.columns = df_d01.columns.str.strip()

        # --- 3. Procesar AT48 ---
        df_at48['Monto Divisa'] = pd.to_numeric(df_at48['Monto Divisa'].str.replace(',', '.', regex=False), errors='coerce').fillna(0)
        df_at48['Contravalor en Bs'] = pd.to_numeric(df_at48['Contravalor en Bs'].str.replace(',', '.', regex=False), errors='coerce').fillna(0)
        condiciones = [
            (df_at48['Movimiento de la Transacción'] == '2') & (df_at48['Tipo de Operación Divisa'] == '7'),
            (df_at48['Movimiento de la Transacción'] == '2') & (df_at48['Tipo de Operación Divisa'] == '3'),
            (df_at48['Movimiento de la Transacción'] == '1') & (df_at48['Tipo de Operación Divisa'] == '7'),
            (df_at48['Movimiento de la Transacción'] == '1') & (df_at48['Tipo de Operación Divisa'] == '3')
        ]
        servicios = [
            'COMPRA DE DIVISAS PAGO INTERNACIONAL WU', 'COMPRA DE DIVISAS EN EFECTIVO',
            'VENTA DE DIVISAS ENVÍO INTERNACIONAL WU', 'VENTA DE DIVISAS EN EFECTIVO'
        ]
        df_at48['Servicio'] = np.select(condiciones, servicios, default='No Identificado')
        df_at48['Servicio'] = df_at48['Servicio'].str.strip()
        df_at48['Servicio'] = df_at48['Servicio'].str.replace('ENVÍO', 'ENVIO', regex=False)

        # --- 4. Procesar D01 ---
        mapeo_servicios = {
            'COMPRA DE DIVISAS PAGO INTERNACIONAL WU D2B ENTRANDO': 'COMPRA DE DIVISAS PAGO INTERNACIONAL WU',
            'ENVIO INTERNACIONAL WU D2B SALIENDO': 'VENTA DE DIVISAS ENVÍO INTERNACIONAL WU'
        }
        df_d01['Servicio'] = df_d01['Servicio'].replace(mapeo_servicios)
        df_d01['Servicio'] = df_d01['Servicio'].str.strip()
        df_d01['Servicio'] = df_d01['Servicio'].str.replace('ENVÍO', 'ENVIO', regex=False)

        # --- INICIO DE LA CORRECCIÓN DE FORMATO NUMÉRICO ---
        # Se elimina el punto de miles y luego se reemplaza la coma decimal
        df_d01['Ppal_USD'] = pd.to_numeric(
            df_d01['Ppal $'].str.replace('.', '', regex=False).str.replace(',', '.', regex=False), 
            errors='coerce'
        ).fillna(0)
        
        df_d01['Ppal Bs.'] = pd.to_numeric(
            df_d01['Ppal Bs.'].str.replace('.', '', regex=False).str.replace(',', '.', regex=False), 
            errors='coerce'
        ).fillna(0)
        # --- FIN DE LA CORRECCIÓN DE FORMATO NUMÉRICO ---

        # --- 5. Calcular Totales ---
        totales_at48 = df_at48.groupby('Servicio').agg(
            Cant_Ops_AT48=('Identificación Tipo Persona RIF', 'count'),
            Monto_USD_AT48=('Monto Divisa', 'sum'),
            Monto_Bs_AT48=('Contravalor en Bs', 'sum')
        )
        totales_d01 = df_d01.groupby('Servicio').agg(
            Cant_Ops_D01=('Cedula', 'count'),
            Monto_USD_D01=('Ppal_USD', 'sum'),
            Monto_Bs_D01=('Ppal Bs.', 'sum')
        )

        # --- 6. Unir y Comparar Resultados ---
        df_final = pd.merge(totales_at48, totales_d01, left_index=True, right_index=True, how='outer').fillna(0)

        for col in ['Cant_Ops_AT48', 'Cant_Ops_D01']: df_final[col] = df_final[col].astype(int)
        for col in ['Monto_USD_AT48', 'Monto_USD_D01', 'Monto_Bs_AT48', 'Monto_Bs_D01']: df_final[col] = df_final[col].astype(float)

        df_final['Diff_Ops'] = df_final['Cant_Ops_AT48'] - df_final['Cant_Ops_D01']
        df_final['Diff_USD'] = df_final['Monto_USD_AT48'] - df_final['Monto_USD_D01']
        df_final['Diff_Bs'] = df_final['Monto_Bs_AT48'] - df_final['Monto_Bs_D01']
        
        def get_observacion(row):
            diff = row['Diff_Ops']
            if diff == 0: return '✅ Coinciden'
            elif diff > 0: return f'Faltan {abs(diff)} ops en D01'
            else: return f'Faltan {abs(diff)} ops en AT48'
        
        df_final['Observacion_Ops'] = df_final.apply(get_observacion, axis=1)

        df_final['Status'] = np.where(
            (df_final['Diff_Ops'] == 0) & 
            (np.isclose(df_final['Diff_USD'], 0)) &
            (np.isclose(df_final['Diff_Bs'], 0)),
            '✅ Correcto', '❌ Error'
        )
        
        df_final = df_final.reset_index().rename(columns={'index': 'Servicio'})
        return df_final.to_json(orient='records')

    except Exception as e:
        return json.dumps([{"error": str(e)}])

if __name__ == "__main__":
    ruta_at48_arg = sys.argv[1]
    ruta_d01_arg = sys.argv[2]
    resultado_json = procesar_reportes(ruta_at48_arg, ruta_d01_arg)
    print(resultado_json)