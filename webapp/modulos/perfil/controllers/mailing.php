<?php
    
    //ini_set('display_errors', 1); error_reporting(E_ALL);
    //Carga la funciones comunes top y footer
    require('common.php');

    //Carga el modelo para este controlador
    require("models/mailing.php");

    class Mailing extends Common
    {
        public $MailingModel;

        function __construct()
        {
            //Se crea el objeto que instancia al modelo que se va a utilizar
            $this->MailingModel = new MailingModel();
        }

        function __destruct()
        {
            //Se destruye el objeto que instancia al modelo que se va a utilizar
            $this->MailingModel->close();
        }

        function guardar()
        {
            echo json_encode($this->MailingModel->guardar($_REQUEST));
        }

        function obtener()
        {
            echo json_encode($this->MailingModel->obtener());
        }

        function restablecer()
        {
            echo json_encode($this->MailingModel->restablecer($_REQUEST));
        }

        function enviarCorreoDePrueba()
        {
            require_once dirname(__DIR__) . '/../phpmailer/sendMail.php';

            if ($mail->Username == 'netwarmonitorsoporte@gmail.com') {
                echo json_encode(['status' => false, 'msg' => 'Los envíos de prueba solo se permiten con usuarios diferentes a "netwarmonitorsoporte@gmail.com".']);
                exit;
            }

            $mail->Subject = 'Prueba de Configuración Mailing';
            $mail->AltBody = 'Prueba de Configuración Mailing. ¡Has configurado correctamente tu servicio para envíos de correos!';
            $content = '<div>Este es un correo de prueba.<br><strong>¡Has configurado correctamente tu servicio para envíos de correos!</strong></div>';
            $mail->MsgHTML($content);
            $mail->AddAddress($mail->Username, $mail->Username);

            if ($mail->Send()) {
                echo json_encode(['status' => true, 'msg' => "¡Prueba exitosa! Se ha enviado un correo de prueba a \"{$mail->Username}\"."]);
            }
            else {
                echo json_encode(['status' => false, 'msg' => "No se ha podido enviar el correo de prueba a \"{$mail->Username}\". Por favor verifique que los datos sean correctos.", 'error' => $mail->ErrorInfo]);
            }
        }
    }

?>