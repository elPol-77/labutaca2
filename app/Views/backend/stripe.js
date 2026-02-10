const stripe = require('stripe')('sk_test_51Syx7APTBNyzobQjQCTV8NYXHjek1Vl1ltougcjbvEkhoprL5NdIH2OqrgvDjyQyMPyOuDZqkqGLUzQEJDFJacNV00p5nd9p91');

async function crearPago() {
  const session = await stripe.checkout.sessions.create({
    payment_method_types: ['card'],
    line_items: [
      {
        price_data: {
          currency: 'usd',
          product_data: {
            name: 'Producto de Prueba',
          },
          unit_amount: 2000, // Cantidad en céntimos (20.00 USD)
        },
        quantity: 1,
      },
    ],
    mode: 'payment',
    success_url: '/success.html', // Usando URL relativa
    cancel_url: '/cancel.html',
  });

  return session.url; // Redirige al usuario a esta URL
}