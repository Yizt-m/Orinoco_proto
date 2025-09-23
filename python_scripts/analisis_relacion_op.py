import sys
import pandas as pd
import json
from thefuzz import fuzz # Librería para comparación difusa de strings
import datetime

def analizar_pagos(ruta_d01, ruta_p01):
    try:
        # --- 1. Cargar Archivos ---
        df_d01 = pd.read_csv(ruta_d01, sep=',', dtype=str)
        df_p01 = pd.read_csv(ruta_p01, sep=';', dtype=str, encoding='latin1')

        # --- 2. Limpiar y Seleccionar Datos ---
        df_d01.columns = df_d01.columns.str.strip()
        df_p01.columns = df_p01.columns.str.strip()
        
        # --- INICIO DE LA CORRECCIÓN 1 ---
        # Usamos los nombres de columna EXACTOS del archivo D01
        cols_d01 = ['MTCN', 'Cedula', 'Cliente', 'Ppal $', 'Ppal Bs.', 'Oficina', 'Usuario']
        df_d01_filtrado = df_d01[cols_d01].copy()
        # --- FIN DE LA CORRECCIÓN 1 ---

        cols_p01 = ['MTCN', 'Field157Value', 'Field158Value']
        # Corregimos 'mtcn' para que coincida en ambos archivos (el del P01 es minúscula)
        df_p01_filtrado = df_p01[cols_p01].copy()


        # --- 3. Preparar Nombres para Comparación ---
        df_d01_filtrado['cliente_norm'] = df_d01_filtrado['Cliente'].str.strip().str.upper()
        df_p01_filtrado['p01_nombre_completo'] = (df_p01_filtrado['Field157Value'].str.strip() + ' ' + df_p01_filtrado['Field158Value'].str.strip()).str.strip()
        df_p01_filtrado['p01_nombre_completo_norm'] = df_p01_filtrado['p01_nombre_completo'].str.upper()

        # --- 4. Cruzar Archivos por MTCN ---
        df_merged = pd.merge(df_d01_filtrado, df_p01_filtrado, on='MTCN', how='inner')

        # --- 5. Lógica de Comparación Difusa de Nombres ---
        def es_pago_correcto(row):
            nombre_d01 = row['cliente_norm']
            nombre_p01 = row['p01_nombre_completo_norm']

            if not nombre_d01 or not nombre_p01: return False
            if nombre_d01 == nombre_p01: return True
            
            score = fuzz.token_set_ratio(nombre_d01, nombre_p01)
            if score >= 88: return True
            
            return False

        df_merged['es_correcto'] = df_merged.apply(es_pago_correcto, axis=1)

        # --- 6. Filtrar Pagos Incorrectos ---
        df_mal_pagadas = df_merged[df_merged['es_correcto'] == False]
        
        # --- 7. Generar Reporte en Excel ---
        if not df_mal_pagadas.empty:
            # --- INICIO DE LA CORRECCIÓN 2 ---
            # Usamos los nombres de columna EXACTOS para el reporte final
            columnas_finales = ['MTCN', 'Cedula', 'Cliente', 'Field157Value', 'Field158Value', 'Ppal $', 'Ppal Bs.', 'Oficina', 'Usuario']
            # --- FIN DE LA CORRECCIÓN 2 ---
            df_reporte = df_mal_pagadas[columnas_finales]
            
            timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
            nombre_archivo = f"Mal_Pagadas_ccz_{timestamp}.xlsx"
            ruta_salida = f"../resultados/{nombre_archivo}"

            df_reporte.to_excel(ruta_salida, index=False)
            
            mensaje = f"Análisis completado. Se encontraron {len(df_reporte)} pagos para revisión manual."
            url_descarga = f"resultados/{nombre_archivo}"
        else:
            mensaje = "¡Análisis completado! Todos los pagos verificados son correctos."
            url_descarga = None
        
        return json.dumps({ "status": "success", "message": mensaje, "download_url": url_descarga })

    except Exception as e:
        return json.dumps({"status": "error", "message": str(e)})

if __name__ == "__main__":
    ruta_d01_arg = sys.argv[1]
    ruta_p01_arg = sys.argv[2]
    resultado_json = analizar_pagos(ruta_d01_arg, ruta_p01_arg)
    print(resultado_json)