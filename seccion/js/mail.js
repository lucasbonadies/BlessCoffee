
function sendEmail(vEmail, token, user_id) {

    emailjs.send("service_f9tn8e9","template_0spxoff",{
        to_name: vEmail,
        from_name: "Recuperación de Contraseña",
        message: "Haga clic en el siguiente enlace para restablecer su contraseña: <br>" +
                    "<a href='http://localhost/ChefSite_v1.0_Prueba/seccion/actualizar_clave.php?token=" + token + "&id_usuario=" + user_id + "'>Restablecer contraseña</a>",
        });
  

   /* var template_params = {
        to_email: vEmail,
        subject: "Recuperación de Contraseña",
        message: "Haga clic en el siguiente enlace para restablecer su contraseña: <br>" +
                "<a href='http://localhost/ChefSite_v1.0_Prueba/seccion/actualizar_clave.php?token=" + token + "&id_usuario=" + user_id + "'>Restablecer contraseña</a>"
    };*/

    // Enviar el correo
   /* emailjs.send("service_f9tn8e9","template_0spxoff", template_params)
        .then(function(response) {
            console.log("Correo enviado correctamente:", response);
            alert("Se ha enviado un correo electrónico con instrucciones para recuperar su contraseña.");
        }, function(error) {
            console.error("Error al enviar el correo:", error);
            alert("Hubo un error al enviar el correo. Revisa la consola para más detalles.");
        });*/
        const serviceID = 'default_service';
        const templateID = 'template_0spxoff';
     
        emailjs.sendForm(serviceID, templateID, template_params)
         .then(() => {
           btn.value = 'Send Email';
           alert('Sent!');
         }, (err) => {
           btn.value = 'Send Email';
           alert(JSON.stringify(err));
         });    
}