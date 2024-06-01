<?php

class ViewAutomato {

    /**
     * Retorna array da quantidade de níveis/colunas e a quantidade de estados diferentes por nível
     * @param type $aEstadosTransicoes
     * @return int
     */
    public function retornaNiveisQntEst($aEstadosTransicoes) {

        //Array da quantidade de níveis e a quantidade de estados diferentes por nível
        $aArrayNiveisQntEst = array();
        //Contador de estados diferentes por nível
        $iQntEst = 0;
        //Contador de niveis
        $iQuant = 0;
        //Controlador
        $iK = 0;
        //Estado maior controlador de níveis
        $iEstCount = 0;
        foreach ($aEstadosTransicoes as $iKey => $aVal) {
            foreach ($aVal as $iEst => $aExp) {
                if ($iK == 0 || $iK < $iEst) {
                    $iK = $iEst;
                    if ($iEst > $iKey) {
                        $iQntEst++;
                    }
                }
            }
            if ($iEstCount == 0 || $iKey > $iEstCount - 1 && $iQntEst != 0) {
                $iQuant++;
                $aArrayNiveisQntEst[$iQuant] = $iQntEst;
                $iEstCount = $iK;
                $iK = 0;
                $iQntEst = 0;
            }
        }
        return $aArrayNiveisQntEst;
    }

    public function montaPaginaAutomato($aEstadosTransicoes, $aTabelaDeTokens, $aTransicoesProprias) {

        // Calcula o número total de círculos
        $numStates = count($aTabelaDeTokens);
        //Retorna os níveis de expansão do automato sendo assim o número de colunas do automato e a quantidade de estados de cada nível
        $aNiveisQntEst = $this->retornaNiveisQntEst($aEstadosTransicoes);
        //Numero de colunas com base nos níveis do automato
        $numColumns = count($aNiveisQntEst);
        // Número de linhas com base no estado 0 que é o com maior saída de transições
        $numRows = count($aEstadosTransicoes[0]);
        // Calcula a largura necessária para o canvas
        $canvasWidth = 50 + $numColumns * 200; // Espaço para o primeiro círculo + número de colunas * espaço entre círculos
        if ($canvasWidth < 700) {
            $canvasWidth = 700;
        }

        // Cabeçalho da página
        $sHtmlTela = '<!DOCTYPE html>
                    <html lang="pt-BR">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Desenhar e Conectar Círculos</title>
                            <style>
                                #canvas { border: 1px solid #000; cursor: pointer; }
                            </style>
                        </head>
                        <body style="text-align:center">
                            <canvas id="canvas" width="' . $canvasWidth . '" height="700"></canvas>
                            <script>
                    ';
        //Inicia as variáveis
        $sHtmlTela .= '         var canvas = document.getElementById("canvas");
                                var ctx = canvas.getContext("2d");
                                var circles = [];
                                var numRows = ' . $numRows . '; // Número de linhas
                                var numColumns = ' . $numColumns . '; // Número de colunas
                                var circleRadius = 20; // Raio do círculo
                                var circleSpacingX = 120; // Espaçamento horizontal entre os círculos
                                var circleSpacingY = canvas.height / (numRows + 1); // Espaçamento vertical entre os círculos
                        ';

        // Função para verificar se o mouse está sobre um círculo
        $sHtmlTela .= '         function isMouseOverCircle(mouseX, mouseY, circle) {
                                    var dx = mouseX - circle.x;
                                    var dy = mouseY - circle.y;
                                    return dx * dx + dy * dy < circle.radius * circle.radius;
                                }
                        ';

        // Evento de clique do mouse
        $sHtmlTela .= '         canvas.addEventListener("mousedown", function(event) {
                                    var mouseX = event.clientX - canvas.getBoundingClientRect().left;
                                    var mouseY = event.clientY - canvas.getBoundingClientRect().top;

                                    circles.forEach(function(circle) {
                                        if (isMouseOverCircle(mouseX, mouseY, circle)) {
                                            circle.isDragging = true;
                                        }
                                    });
                                });
                        ';
        $sHtmlTela .= ' 
                                // Evento de movimento do mouse
                                canvas.addEventListener("mousemove", function(event) {
                                    circles.forEach(function(circle) {
                                        if (circle.isDragging) {
                                            circle.x = event.clientX - canvas.getBoundingClientRect().left;
                                            circle.y = event.clientY - canvas.getBoundingClientRect().top;
                                            redraw();
                                        }
                                    });
                                });

                                // Evento de soltar o botão do mouse
                                canvas.addEventListener("mouseup", function() {
                                    circles.forEach(function(circle) {
                                        circle.isDragging = false;
                                    });
                                });
                        ';

        // Função para desenhar os círculos
        $sHtmlTela .= '         function drawCircle(circle) {
                                    ctx.beginPath();
                                    ctx.arc(circle.x, circle.y, circle.radius, 0, 2 * Math.PI);
                                    ctx.fillStyle = "rgba(0, 149, 221, 0.5)";
                                    ctx.fill();
                                    ctx.strokeStyle = "#0095DD";
                                    ctx.lineWidth = 2;
                                    ctx.stroke();
                                    ctx.closePath();

                                    ctx.fillStyle = "#000";
                                    ctx.font = "12px Arial";
                                    ctx.textAlign = "center";
                                    ctx.textBaseline = "middle";
                                    ctx.fillText(circle.label, circle.x, circle.y);

                       ';

        //Desenha o estado inicial
        $sHtmlTela .= '             // Desenha a flecha apontando para o estado inicial q0
                                        if (circle.label === "q0") {
                                            ctx.beginPath();
                                            ctx.moveTo(circle.x - circle.radius, circle.y);
                                            ctx.lineTo(circle.x - circle.radius - 20, circle.y - 10);
                                            ctx.lineTo(circle.x - circle.radius - 20, circle.y + 10);
                                            ctx.closePath();
                                            ctx.fillStyle = "rgba(200, 200, 200, 0.5)"; // Cinza claro para o preenchimento
                                            ctx.fill();
                                            ctx.strokeStyle = "#0095DD"; // Azul para a borda
                                            ctx.lineWidth = 2;
                                            ctx.stroke();
                                        }
                       ';

        $aEstDes = array(); //Armazena estados finais já desenhados
        foreach ($aEstadosTransicoes as $iKey => $aVal) {

            foreach ($aVal as $iEst => $aExp) {
                if ($aTabelaDeTokens[$iKey] != "?" && !in_array($iKey, $aEstDes)) {
                    //Desenha a borda dupla do circulo do estado final
                    $sHtmlTela .= '                                
                                        // Desenha a borda dupla para o círculo dos estados finais
                                        if (circle.label === "q' . $iKey . '") {
                                            ctx.beginPath();
                                            ctx.arc(circle.x, circle.y, circle.radius - 4, 0, 2 * Math.PI); // Aumenta o raio para a borda dupla
                                            ctx.strokeStyle = "#0095DD"; // Azul para a cor da primeira borda
                                            ctx.lineWidth = 2;
                                            ctx.stroke();
                                            ctx.closePath();
                                        }

                        ';

                    $aEstDes[] = $iKey;
                }
                if ($iKey != $iEst) {
                    $sHtmlTela .= '                                
                                        if (circle.label === "q' . $iKey . '") {
                                            // Desenha a linha conectando as bordas dos círculos
                                            ctx.beginPath();
                                            ctx.moveTo(circle.x + circle.radius * Math.cos(Math.atan2(circles[' . $iEst . '].y - circle.y, circles[' . $iEst . '].x - circle.x)), circle.y + circle.radius * Math.sin(Math.atan2(circles[' . $iEst . '].y - circle.y, circles[' . $iEst . '].x - circle.x)));
                                            ctx.lineTo(circles[' . $iEst . '].x - circles[' . $iEst . '].radius * Math.cos(Math.atan2(circles[' . $iEst . '].y - circle.y, circles[' . $iEst . '].x - circle.x)), circles[' . $iEst . '].y - circles[' . $iEst . '].radius * Math.sin(Math.atan2(circles[' . $iEst . '].y - circle.y, circles[' . $iEst . '].x - circle.x)));

                                            // Adiciona a seta na ponta da linha
                                            var arrowSize = 10; // Tamanho da seta
                                            var angle = Math.atan2(circles[' . $iEst . '].y - circle.y, circles[' . $iEst . '].x - circle.x);
                                            var circles' . $iEst . 'EdgeX = circles[' . $iEst . '].x - circles[' . $iEst . '].radius * Math.cos(angle);
                                            var circles' . $iEst . 'EdgeY = circles[' . $iEst . '].y - circles[' . $iEst . '].radius * Math.sin(angle);
                                            ctx.moveTo(circles' . $iEst . 'EdgeX, circles' . $iEst . 'EdgeY);
                                            ctx.lineTo(circles' . $iEst . 'EdgeX - arrowSize * Math.cos(angle - Math.PI / 6), circles' . $iEst . 'EdgeY - arrowSize * Math.sin(angle - Math.PI / 6));
                                            ctx.moveTo(circles' . $iEst . 'EdgeX, circles' . $iEst . 'EdgeY);
                                            ctx.lineTo(circles' . $iEst . 'EdgeX - arrowSize * Math.cos(angle + Math.PI / 6), circles' . $iEst . 'EdgeY - arrowSize * Math.sin(angle + Math.PI / 6));

                                            ctx.strokeStyle = "#0095DD"; // Azul para a linha de conexão
                                            ctx.lineWidth = 2;
                                            ctx.stroke();
                                            ctx.closePath();
                                            
                                            // Adiciona o rótulo à linha entre circle1 e circle2
                                            ctx.beginPath();
                                            var labelX' . $iEst . ' = (circle.x + circles[' . $iEst . '].x) / 2;
                                            var labelY' . $iEst . ' = (circle.y + circles[' . $iEst . '].y) / 2;
                                            ctx.fillStyle = "#000";
                                            ctx.font = "12px Arial";
                                            ctx.textAlign = "center";
                                            ctx.textBaseline = "middle";
                                            ctx.fillText("' . $aExp[0] . '", labelX' . $iEst . ', labelY' . $iEst . '-10);
                                            ctx.closePath();
                                        }

                        ';
                }
            }
            if (isset($aTransicoesProprias[$iKey])) { // Verifica se há transição para o próprio estado 
                if ($aTransicoesProprias[$iKey]) {
                    $sHtmlTela .= '                                
                                       
                                        if (circle.label === "q' . $iKey . '") {
                                            // Desenha a linha conectando circle e circle (criando uma pétala) quando estado que possui transição para ele mesmo
                                            ctx.beginPath();
                                            ctx.moveTo(circle.x, circle.y - circle.radius);
                                            ctx.bezierCurveTo(circle.x - 50, circle.y - 70, circle.x + 50, circle.y - 70, circle.x, circle.y - circle.radius);
                                            ctx.strokeStyle = "#0095DD"; // Azul para a linha de conexão
                                            ctx.lineWidth = 2;
                                            ctx.stroke();

                                            // Adiciona o rótulo à linha entre circle e ele mesmo
                                            var labelXcurva' . $iKey . ' = circle.x;
                                            var labelYcurva' . $iKey . ' = circle.y - 65;
                                            ctx.fillStyle = "#000";
                                            ctx.font = "12px Arial";
                                            ctx.textAlign = "center";
                                            ctx.textBaseline = "middle";
                                            ctx.fillText("' . $aVal[$iKey][0] . '", labelXcurva' . $iKey . ', labelYcurva' . $iKey . ');
                                            ctx.closePath();
                                            
                                            // Adiciona a seta na ponta da curva de Bézier
                                            var arrowSize = 10; // Tamanho da seta
                                            var endX = circle.x; // Ponto final da curva (mesmo ponto inicial, pois é uma auto-transição)
                                            var endY = circle.y - circle.radius;
                                            var angle = Math.PI / 1.35; // Ângulo da seta 

                                            // Desenha a seta na ponta da pétala
                                            ctx.beginPath();
                                            ctx.moveTo(endX, endY);
                                            ctx.lineTo(endX - arrowSize * Math.cos(angle - Math.PI / 6), endY - arrowSize * Math.sin(angle - Math.PI / 6));
                                            ctx.moveTo(endX, endY);
                                            ctx.lineTo(endX - arrowSize * Math.cos(angle + Math.PI / 6), endY - arrowSize * Math.sin(angle + Math.PI / 6));

                                            ctx.strokeStyle = "#0095DD"; // Azul para a linha de conexão
                                            ctx.lineWidth = 2;
                                            ctx.stroke();
                                            ctx.closePath();
                                        }

                        ';
                }
            }
            // Adiciona um rótulo ao círculo circle
            $sHtmlTela .= '     if (circle.label === "q' . $iKey . '") {
                                                    ctx.fillStyle = "#000";
                                                    ctx.font = "12px Arial";
                                                    ctx.textAlign = "center";
                                                    ctx.textBaseline = "middle";
                                                    ctx.fillText("' . $aTabelaDeTokens[$iKey] . '", circle.x, circle.y - circle.radius + 50);
                                                    
                                        }';
        }




        $sHtmlTela .= '                        }
                        ';

        // Função para redesenhar toda a tela
        $sHtmlTela .= '         function redraw() {
                                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                                    circles.forEach(function(circle) {
                                        drawCircle(circle);
                                    });
                                }
                        ';

        // Adiciona o estado q0 separadamente
        $sHtmlTela .= '         circles.push({ x: circleSpacingX, y: canvas.height / 2, radius: circleRadius, label: "q0" });
                        ';

        // Loop para adicionar os outros estados
        $iCont = 0;
        $iEspInicial = 0;
        foreach ($aNiveisQntEst as $iKey => $iVal) {
            $col = (int) (($iKey - 1));
            $iEspInicial = ($numRows - $iVal) / 2;
            for ($i = 1; $i < $iVal + 1; $i++) {
                $row = ($i - 1);
                $iCont++;
                $sHtmlTela .= '     var x = circleSpacingX * (' . $col . ' + 2); // Começa da segunda coluna
                                    var y = circleSpacingY * (' . $row + $iEspInicial . ' + 1);
                                    circles.push({ x: x, y: y, radius: circleRadius, label: "q" + ' . $iCont . ' });
                    ';
            }
        }

        // Desenhar os círculos pela primeira vez e final do html
        $sHtmlTela .= '         redraw();
                            </script>
                        </body>
                    </html>';

        return $sHtmlTela;
    }

}
