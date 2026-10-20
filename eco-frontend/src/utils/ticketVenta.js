// Nombre que sale arriba del ticket (cámbialo por el de tu minisúper)
const nombreNegocio = 'La Económica'

export const formatearFechaSolo = (fechaRaw) => {
  if (!fechaRaw) return 'N/A'
  const objFecha = new Date(fechaRaw)
  return isNaN(objFecha.getTime()) ? fechaRaw : objFecha.toLocaleDateString()
}

export const formatearHoraSolo = (fechaRaw) => {
  if (!fechaRaw) return 'N/A'
  const objFecha = new Date(fechaRaw)
  if (isNaN(objFecha.getTime())) return 'N/A'
  return objFecha.toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

export const nombreCompleto = (u) => {
  if (!u) return ''
  const nombre = u.nombre || u.name || u.username || ''
  const apellido = u.apellido || u.apellidos || u.last_name || ''
  return `${nombre} ${apellido}`.trim()
}

// Nombre del usuario que emitió la venta
export const nombreVendedor = (venta) => {
  if (venta?.vendedor_nombre) return venta.vendedor_nombre
  if (!venta?.usuario) return 'Sin registro'
  return nombreCompleto(venta.usuario) || `Usuario #${venta.user_id}`
}

// Evita que un nombre con símbolos rompa el ticket
const limpiarTexto = (texto) => {
  return String(texto ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
}

// Abre el ticket Ultra Compacto para impresoras de 58(48)mm y lo manda a imprimir
export const imprimirTicket = (venta) => {
  if (!venta) return

  const fechaRaw = venta.fecha_venta || venta.created_at
  const detalles = venta.detalles || []

  let filas = ''
  detalles.forEach((det) => {
    const nombre = limpiarTexto(det.producto_nombre || det.producto?.nombre || 'Producto')
    filas += `
      <div class="producto">${nombre}</div>
      <div class="linea">
        <span>${det.cantidad} x $${Number(det.precio_unitario).toFixed(2)}</span>
        <span>$${Number(det.subtotal).toFixed(2)}</span>
      </div>
    `
  })

  const html = `
    <html>
      <head>
        <title>Ticket ${limpiarTexto(venta.factura || venta.id)}</title>
        <style>
          @page {
            size: 58mm auto;
            margin: 0mm; /* Elimina margenes por defecto de la impresora */
          }
          * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
          }
          body {
            width: 38mm; /* Ancho ultra compacto para 58(48)mm */
            margin: 0 auto;
            padding: 2mm 0;
            font-family: 'Courier New', monospace;
            font-size: 8px; /* Letra ultra compacta */
            line-height: 1.1;
            color: #000;
          }
          .centro { text-align: center; }
          .negocio { font-size: 10px; font-weight: bold; }
          .separador { border-top: 1px dashed #000; margin: 3px 0; }
          .linea { display: flex; justify-content: space-between; width: 100%; }
          .producto { font-weight: bold; margin-top: 2px; word-break: break-all; }
          .total { font-size: 9px; font-weight: bold; }
        </style>
      </head>
      <body>
        <div class="centro negocio">${limpiarTexto(nombreNegocio)}</div>
        <div class="centro">Ticket de venta</div>
        <div class="separador"></div>

        <div>Factura: ${limpiarTexto(venta.factura || '#' + venta.id)}</div>
        <div>Fecha: ${formatearFechaSolo(fechaRaw)}</div>
        <div>Hora: ${formatearHoraSolo(fechaRaw)}</div>
        <div>Cliente: ${limpiarTexto(venta.cliente || 'Consumidor Final')}</div>
        <div>Atendió: ${limpiarTexto(nombreVendedor(venta))}</div>
        <div class="separador"></div>

        ${filas}

        <div class="separador"></div>
        <div class="linea total"><span>TOTAL</span><span>$${Number(venta.total || 0).toFixed(2)}</span></div>
        <div class="linea"><span>Recibido</span><span>$${Number(venta.dinero_recibido || 0).toFixed(2)}</span></div>
        <div class="linea"><span>Cambio</span><span>$${Number(venta.vuelto || 0).toFixed(2)}</span></div>
        <div class="separador"></div>

        <div class="centro">¡Gracias por su compra!</div>
      </body>
    </html>
  `

  const ventana = window.open('', '_blank', 'width=400,height=600')
  if (!ventana) {
    alert('El navegador bloqueó la ventana del ticket. Permite las ventanas emergentes e intenta de nuevo.')
    return
  }
  ventana.document.write(html)
  ventana.document.close()
  ventana.focus()
  setTimeout(() => {
    ventana.print()
    ventana.close()
  }, 300)
}