document.addEventListener("DOMContentLoaded", () => {
    
    // 1. Lógica de Transição de Status Manual
    const botoesMudarStatus = document.querySelectorAll(".btn-mudar-status");

    botoesMudarStatus.forEach(botao => {
        botao.addEventListener("click", (e) => {
            const card = e.target.closest(".card-curso");
            const valorStatus = card.querySelector(".valor-status");
            let statusAtual = valorStatus.textContent.trim();

            if (statusAtual === "Planejado") {
                valorStatus.textContent = "Em andamento";
            } else if (statusAtual === "Inscrições abertas") {
                valorStatus.textContent = "Inscrições em aberto";
            } else if (statusAtual === "Concluído") {
                valorStatus.textContent = "Cancelado";
            } else {
                valorStatus.textContent = "Planejado";
            }
        });
    });

    // 2. Função de Atualização Automática baseada nas Datas do Curso
    function atualizarStatusAutomatico() {
        const hojeStr = new Date().toISOString().split("T")[0]; // Data atual YYYY-MM-DD
        const cards = document.querySelectorAll(".card-curso");

        cards.forEach(card => {
            const dataInicio = card.getAttribute("data-inicio");
            const dataFim = card.getAttribute("data-fim");
            const valorStatus = card.querySelector(".valor-status");

            if (dataInicio && dataFim) {
                if (hojeStr === dataFim || hojeStr > dataFim) {
                    valorStatus.textContent = "Concluído";
                } else if (hojeStr >= dataInicio) {
                    valorStatus.textContent = "Em andamento";
                }
            }
        });
    }

    atualizarStatusAutomatico();

    // 3. Sistema de Notificações Pop-ups (Canto Inferior Direito)
    const containerNotificacoes = document.getElementById("container-notificacoes");

    function dispararPopup(titulo, mensagem) {
        if (!containerNotificacoes) return;

        const popup = document.createElement("div");
        popup.classList.add("popup-notificacao");
        
        popup.innerHTML = `
            <button class="fechar-popup">&times;</button>
            <h4>${titulo}</h4>
            <p>${mensagem}</p>
        `;

        const btnFechar = popup.querySelector(".fechar-popup");
        btnFechar.addEventListener("click", () => {
            popup.remove();
        });

        containerNotificacoes.appendChild(popup);

        // Remove automaticamente após 8 segundos
        setTimeout(() => {
            if (popup.parentElement) {
                popup.remove();
            }
        }, 8000);
    }

    // Alertas importantes configurados
    setTimeout(() => {
        dispararPopup(
            "Aviso Importante: Curso Próximo", 
            "Faltam 5 dias para o curso começar. Sugerimos alertar os alunos inscritos."
        );
    }, 2000);

    setTimeout(() => {
        dispararPopup(
            "Encerramento de Curso", 
            "Hoje é o dia de encerramento de um curso cadastrado."
        );
    }, 4500);

    // 4. Aplicação Dinâmica das Cores nas Pílulas da Lista de Cursos
    const cardsCursos = document.querySelectorAll(".card-curso");
    cardsCursos.forEach(card => {
        const categoria = card.getAttribute("data-categoria");
        const pilula = card.querySelector(".pilula-cor");
        
        if (pilula && categoria) {
            pilula.className = "pilula-cor";
            if (categoria === "estetica") pilula.classList.add("bg-roxo");
            else if (categoria === "gastronomia") pilula.classList.add("bg-verde");
            else if (categoria === "saude") pilula.classList.add("bg-vermelho");
            else if (categoria === "tecnologia") pilula.classList.add("bg-azul");
            else if (categoria === "outro") pilula.classList.add("bg-amarelo");
        }
    });

});