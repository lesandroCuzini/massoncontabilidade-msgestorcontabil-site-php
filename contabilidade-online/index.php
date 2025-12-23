<?php
    ini_set('display_errors',1);
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_COMPILE_ERROR);

    include_once '../lib/setup/common.php';

    $acao = isset($_POST['acao']) ? clearInjection($_POST['acao']) : '';
    if ($acao == 'enviar') {
        require_once("../controller/class.phpmailer.php");
        require_once("../controller/class.smtp.php");

        $nome = isset($_POST['nome']) ? clearInjection($_POST['nome']) : '';
        $email = isset($_POST['email']) ? clearInjection($_POST['email']) : '';
        $telefone = isset($_POST['telefone']) ? clearInjection($_POST['telefone']) : '';
        $mensagem = isset($_POST['mensagem']) ? clearInjection($_POST['mensagem']) : '';

        //Envia o e-mail
        date_default_timezone_set('America/Sao_Paulo');

        ini_set("allow_url_fopen", 1);
        $assunto = utf8_decode('Contato da Landing Page do Site');
        $remetente = 'contato.site@msgestorcontabilonline.com.br';

        $conteudo = file_get_contents("../view/mails/landing_page.html");
        $conteudo = str_replace("##base_url_site##", "https://www.msgestorcontabilonline.com.br", $conteudo);
        $conteudo = str_replace("##hora##", date("d/m/Y H:i:s"), $conteudo);
        $conteudo = str_replace("##assunto##", $assunto, $conteudo);
        $conteudo = str_replace("##nome##", $nome, $conteudo);
        $conteudo = str_replace("##email##", $email, $conteudo);
        $conteudo = str_replace("##telefone##", $telefone, $conteudo);
        $conteudo = str_replace("##mensagem##", nl2br($mensagem), $conteudo);
        $conteudo = wordwrap($conteudo);

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8";
        $headers .= 'From: ' . $remetente . '>' . "\r\n";

        // Inicia a classe PHPMailer
        $mailer = new PHPMailer();
        $mailer->IsSMTP();
        $mailer->SMTPDebug = false;
        $mailer->SMTPAuth = true;
        $mailer->Host = 'mail.msgestorcontabilonline.com.br';
        $mailer->Port =  587;
        $mailer->Username = 'contato.site@msgestorcontabilonline.com.br';
        $mailer->Password = 'sitegestor1234';
        $mailer->FromName = "Contato Ms Gestor Contabil Online";
        $mailer->From = 'contato.site@msgestorcontabilonline.com.br';
        $mailer->AddAddress('contato@msgestorcontabilonline.com.br', utf8_decode('Contato Ms Gestor Contabil Online'));
        $mailer->IsHTML(true);
        $mailer->Subject = "Contato realizado na site landing page.";
        $mailer->Body = $conteudo;
        $envio = $mailer->Send();

        if ($envio) {
            echo "<script>alert('Contato enviado com sucesso. Em breve entraremos em contato!');</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="3Dots">
    <meta name="theme-color" content="#FFFFFF">
    <meta name="twitter:card" value="summary">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>MS - Gestor Contábil Online</title>
    <!-- Favicon -->
    <link href="img/favicon.png" rel="shortcut icon" type="image/png"/>
    <link href="img/favicon.ico" rel="shortcut" type="image/x-icon"/>
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <!-- Swiper -->
    <link rel="stylesheet" type="text/css" href="css/swiper-bundle.min.css"/>
    <!-- Fancybox -->
    <link rel="stylesheet" type="text/css" href="css/jquery.fancybox.min.css"/>
    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="css/fonts.css"/>
    <!-- Home -->
    <link rel="stylesheet" type="text/css" href="css/home.css"/>

    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-K4V4PQD');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google Tag Manager (noscript) -->
    <noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K4V4PQD" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-814193638"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'AW-814193638');
    </script>

    <script>
        function gtag_report_conversion(url) {
            var callback = function () {
                if (typeof(url) != 'undefined') {
                    window.location = url;
                }
            };
            gtag('event', 'conversion', { 'send_to': 'AW- 814193638/CvxxCK3uk4AYEOa3noQD', 'event_callback': callback });
            return false;
        }
    </script>
</head>
<body>
    <header class="header">
        <nav class="navbar shadow-sm">
            <div class="container">
                <div class="navbar-brand">
                    <img src="img/logo_ms.png" class="img-fluid" alt="Logo MS Contabilidade Online">
                </div>
                <ul class="navbar-nav mx-auto mx-md-0 ms-md-auto">
                    <li class="nav-item">
                        <a href="tel:+551637236749" class="nav-link text-black lh-1 fs-3 fw-700 p-0">
                            <img src="img/icones/icone_telefone.png" class="img-fluid" alt="Icone telefone para contato">
                            <span>(16) 3723-6749</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <section class="banner">
        <div class="container">
            <div class="wrap">
                <div class="wrap-text">
                    <h1 class="section-title text-white fw-700 mb-3">Abertura de Empresa</h1>
                    <h6 class="text-white fw-400 fs-2 lh-base mb-0">Abra já a sua empresa de forma rápida,  com eficiência, segurança  e agilidade para atender a sua necessidade e deixa-lo tranquilo quanto a contabilidade de sua empresa!</h6>
                </div>
                <div class="wrap-form">
                    <form method="post" class="form-contact">
                        <input type="hidden" name="acao" value="enviar" />
                        <h5 class="text-green text-center fw-600 fs-3 mb-3">Solicite uma proposta</h5>
                        <div class="mb-3">
                            <input type="text" name="nome" id="nome" class="form-control" placeholder="NOME:" required="required" />
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" id="email" class="form-control" placeholder="E-MAIL:" required="required" />
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="telefone" id="telefone" class="form-control sp_celphones" placeholder="TELEFONE:" required="required" />
                        </div>
                        <div class="mb-4">
                            <textarea name="mensagem" id="mensagem" class="form-control" rows="5" placeholder="COMO PODEMOS TE AJUDAR?" required="required"></textarea>
                        </div>
                        <button type="submit" class="btn btn-green">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="migrate">
        <div class="container">
            <div class="wrap">
                <div class="wrap-img">
                    <img src="img/video.jpg" class="img-fluid" alt="Migre para gestor contábil online">
                    <a href="https://www.youtube.com/watch?v=L6ayoop5f1Y" class="d-flex align-items-center justify-content-center" data-fancybox>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M188.3 147.1C195.8 142.8 205.1 142.1 212.5 147.5L356.5 235.5C363.6 239.9 368 247.6 368 256C368 264.4 363.6 272.1 356.5 276.5L212.5 364.5C205.1 369 195.8 369.2 188.3 364.9C180.7 360.7 176 352.7 176 344V167.1C176 159.3 180.7 151.3 188.3 147.1V147.1zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                        </svg>
                    </a>
                </div>
                <div class="wrap-text">
                    <h1 class="section-title text-black fw-700 mb-4">Migre para Gestor Contábil Online</h1>
                    <p class="text-black fs-1 mb-0">A contabilidade online nada mais é do que a gestão contábil de uma empresa, através de uma plataforma digital. É uma maneira simplificada de operar os processos contábeis, trazendo eficiência, agilidade e otimização, tanto para o escritório de contabilidade quanto para o cliente.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="benefits">
        <div class="container">
            <h1 class="section-title text-black text-center fw-700">Saiba quais os benefícios e vantagens que o Gestor Contábil Online pode te oferecer</h1>
            <div class="benefits-item">
                <img src="img/beneficio.png" class="img-fluid" alt="Benefício do gestor contábil online">
                <div class="benefits-item--text">
                    <h5 class="fs-3 fw-700 text-uppercase text-black">Benefício</h5>
                    <p>Uma das principais vantagens de investir em contabilidade online é ter a sensação real de confiança e segurança ao longo dos processos, visto que os contadores conhecem a legislação e contribuem para um controle financeiro dentro das normas exigidas, reduzindo o risco de quaisquer infrações, multas ou problemas.</p>
                </div>
            </div>
            <div class="benefits-item">
                <img src="img/vantagens.png" class="img-fluid" alt="Vantagens do gestor contábil online">
                <div class="benefits-item--text">
                    <h5 class="fs-3 fw-700 text-uppercase text-black">Vantagens</h5>
                    <p>Quando organizada e em dia, a contabilidade é capaz de oferecer informações relevantes para a tomada de decisão de gestores, além de prever situações de risco para a empresa, tanto no presente tanto no futuro. Não é atoa que se diz por ai que o contador é uma peça fundamental para a saúde financeira de um negócio.</p>
                </div>
            </div>
            <div class="benefits-item">
                <img src="img/preco.png" class="img-fluid" alt="Preço diferenciado do gestor contábil online">
                <div class="benefits-item--text">
                    <h5 class="fs-3 fw-700 text-uppercase text-black">Preço Diferenciado</h5>
                    <p>Devido o envio de documentos ser através de plataformas digitais, é possível reduzir o custo do serviço, proporcionando ao cliente um melhor preço!</p>
                </div>
            </div>
            <a onclick="return gtag_report_conversion('https://wa.me/+5516999881320');" href="https://wa.me/+5516999881320" target="_self" class="btn btn-green icon-whatsapp">Falar com Especialista</a>
        </div>
    </section>
    <section class="testimony">
        <div class="container">
            <h1 class="section-title text-black text-center fw-700 mb-5">Quem conhece, recomenda.</h1>
            <div class="swiper-container position-relative mb-5">
                <div class="swiper swiper-testimony">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="card">
                                <ul class="stars">
                                    <li><img src="img/icones/icone_estrela.png" class="img-fluid"></li>
                                    <li><img src="img/icones/icone_estrela.png" class="img-fluid"></li>
                                    <li><img src="img/icones/icone_estrela.png" class="img-fluid"></li>
                                    <li><img src="img/icones/icone_estrela.png" class="img-fluid"></li>
                                    <li><img src="img/icones/icone_estrela.png" class="img-fluid"></li>
                                </ul>
                                <p class="text-center mb-0">Sou cliente do MS Gestor contábil online desde 22/12/2017, desde o primeiro contato foram super prestativos e ágeis em tirar minhas dúvidas, e com a orientação da equipe do MS gestor, não tive nenhum problema com o fisco.</p>
                                <p class="text-center mb-0">Super indico o trabalho do MS GESTOR, são pessoas capacitadas e confiáveis que posso confiar tranquilamente.</p>
                                <p class="text-center mb-0">Parabéns a todos da equipe!!!</p>
                            </div>
                            <div class="card-author">
                                <div class="photo">
                                    <img src="img/icones/icone_usuario.png" class="img-fluid" alt="Autor do depoimento">
                                </div>
                                <div class="author">
                                    <h6 class="fs-1 fw-700 text-black">Flávia Lopes</h6>
                                    <h6 class="fs-1 fw-600 text-black mb-0">J.W.L Serviços Educacionais LTDA</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
            <a onclick="return gtag_report_conversion('https://wa.me/+5516999881320');" href="https://wa.me/+5516999881320" target="_self" class="btn btn-green icon-whatsapp">Falar com Especialista</a>
        </div>
    </section>
    <section class="about">
        <div class="container">
            <div class="wrap">
                <div class="wrap-img">
                    <img src="img/quem_somos.jpg" class="img-fluid" alt="Quem somos">
                </div>
                <div class="wrap-text">
                    <h1 class="section-title text-black fw-700 mb-4">Quem Somos</h1>
                    <p class="text-black fs-1 mb-0">Ms Gestor Contábil Online é um Escritório de Contabilidade Online com sede na cidade de Franca, fundado em 2017 pelo proprietário do Escritório Contábil Masson Serviços Contábeis e Auditoria Ltda-ME, com 30 anos de experiência em contabilidade, decidimos acompanhar as novas tendências tecnológicas e também oferecer serviços de Contabilidade Online, que devido à redução de despesas em relação ao escritório convencional, possibilita oferecer uma mensalidade mais acessível para novos clientes, que podem economizar e investir mais em seu negócio, um fator muito importante para que as micro e pequenas empresas, se mantenham no mercado.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="questions">
        <div class="container">
            <h1 class="section-title text-black text-center fw-700 mb-5">Perguntas Frequentes</h1>
            <div class="accordion" id="accordionQuestions">
                <div class="accordion-item">
                    <div id="headingOne" class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#questionOne" aria-expanded="false" aria-controls="questionOne">
                            Quais são os primeiros passos para abrir um negócio?
                        </button>
                    </div>
                    <div id="questionOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionQuestions">
                        <div class="accordion-body">
                            Criação de um plano de negócio, elaboração de um contrato social ou ato constitutivo, registro na junta comercial, obtenção de CNPJ e licenciamento.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div id="headingTwo" class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#questionTwo" aria-expanded="false" aria-controls="questionTwo">
                            Qual é o prazo para abertura de empresa?
                        </button>
                    </div>
                    <div id="questionTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionQuestions">
                        <div class="accordion-body">
                            A abertura do CNPJ leva em média 5 a 7 dias úteis para registro na Junta Comercial, Receita Federal e Secretaria da Fazenda do Estado em média de 5 dias úteis para Inscrição Municipal em média de 10 a 15 dias úteis para credenciamento no regime tributário do Simples Nacional. Prazo médio total é de 20 a 25 dias úteis.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div id="headingThree" class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#questionThree" aria-expanded="false" aria-controls="questionThree">
                            Como funciona o processo de alteração de MEI para ME?
                        </button>
                    </div>
                    <div id="questionThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionQuestions">
                        <div class="accordion-body">
                            1º Passo: Desenquadramento do SIMEI. 2º Passo Pedido de Viabilidade. 3º Passo: Solicitação de alteração dos dados da empresa na Receita Federal.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div id="headingFour" class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#questionFour" aria-expanded="false" aria-controls="questionFour">
                            Qual é o valor máximo do faturamento anual do Microempreendedor Individual?
                        </button>
                    </div>
                    <div id="questionFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionQuestions">
                        <div class="accordion-body">
                            De até R$ 81.000,00 por ano, de janeiro a dezembro. O Microempreendedor Individual que se formalizar durante o ano em curso, tem seu limite de faturamento proporcional a R$ 6.750,00, por mês, até 31 de dezembro do mesmo ano. Exemplo: O MEI que se formalizar em junho, terá o limite de faturamento de R$ 47.250,00 (7 meses x R$ 6.750,00), neste ano.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div id="headingFive" class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#questionFive" aria-expanded="false" aria-controls="questionFive">
                            Qual a diferença entre Empresário Individual e Sociedade Limitada?
                        </button>
                    </div>
                    <div id="questionFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionQuestions">
                        <div class="accordion-body">
                            A sociedade limitada (LTDA) é aquela formada por duas ou mais pessoas, podendo ser pessoa natural ou jurídica, com capital social dividido em quotas. A responsabilidade de cada sócio é limitada ao valor de suas quotas, mas todos os sócios respondem solidariamente pela integralização do capital social. O patrimônio pessoal dos sócios fica separado do patrimônio da empresa. Assim, caso haja algum problema financeiro relevante, ou mesmo falência, os bens do empreendedor não podem ser utilizados para quitação das dívidas. Empresário Individual. Trata-se de uma atividade composta apenas pelo proprietário da empresa não precisa de sócios. Não é preciso integrar um valor mínimo de Capital Social. Por outro lado, o patrimônio pessoal do empreendedor fica atrelado ao patrimônio da empresa. Isso quer dizer que, no caso de dívidas ou falência, todos os seus bens podem ser usados para quitação desses possíveis débitos.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div id="headingSix" class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#questionSix" aria-expanded="false" aria-controls="questionSix">
                            Tem como uma empresa MEI (Microempreendedor Individual) ser transformada em uma ME (Micro Empresa) optante pelo Simples Nacional?
                        </button>
                    </div>
                    <div id="questionSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionQuestions">
                        <div class="accordion-body">
                            Sim, a transformação do MEI em ME pode ser feita qualquer momento, por opção própria do empreendedor, ou por comunicação obrigatória, nos seguintes casos: Faturamento bruto acima do limite anual (R$ 81 mil) Contratação de mais de um funcionário.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact">
        <div class="container">
            <div class="row gap-5 gap-lg-0">
                <div class="col-lg-7">
                    <img src="img/logo_footer.png" class="img-fluid" alt="Logo Ms Gestor Contábil Online">
                    <h1 class="title text-white fw-700">Migre agora mesmo para Gestor Contábil Online</h1>
                    <p class="subtitle text-white fs-2 mb-0">Tenha um contador online e cresça ainda mais.</p>
                    <p class="subtitle text-white fs-2 mb-0">Nossa equipe está sempre pronta para te atender!</p>
                </div>
                <div class="col-lg-5">
                    <div class="card-contact">
                        <p class="fs-1 text-black text-center fw-500">Entre em contato agora mesmo com nossos especialistas, não perca essa oportunidade!</p>
                        <a onclick="return gtag_report_conversion('https://wa.me/+5516999881320');" href="https://wa.me/+5516999881320" target="_self" class="btn btn-green icon-whatsapp">Falar com Especialista</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer">
        <div class="container">
            <div class="text-white text-center">
                <p class="fs-1 lh-1 mb-1">Ms Gestor Contábil Online. &copy; Todos os direitos reservados - 2022</p>
                <p class="fs-1 lh-1 mb-1">CNPJ: 05.348.619/0001-05</p>
                <p class="fs-1 lh-1 mb-2">CRC: 1SP155999/O-0</p>
                <p class="fs-tiny lh-1 mb-0">Desenvolvido por <a href="https://www.3dots.com.br/" target="_blank" class="text-white">3dots</a></p>
            </div>
        </div>
    </footer>

    <a onclick="return gtag_report_conversion('https://wa.me/+5516999881320');" href="https://wa.me/+5516999881320" target="_self" class="btn btn-floating">
        <img src="img/icones/icone_whatsapp.png" class="img-fluid" alt="Converse no WhatsApp com a MS Gestor Contábil Online">
    </a>

    <!-- Jquery -->
    <script src="js/jquery.min.js"></script>
    <!-- Jquery Mask -->
    <script src="js/jquery.mask.min.js"></script>
    <!-- Bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- Swiper -->
    <script src="js/swiper-bundle.min.js"></script>
    <!-- Fancybox -->
    <script src="js/jquery.fancybox.min.js"></script>
    <!-- Home JS -->
    <script src="js/home.js"></script>
</body>
</html>