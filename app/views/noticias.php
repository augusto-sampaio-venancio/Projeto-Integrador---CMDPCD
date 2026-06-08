<?php require_once __DIR__ . '/partials/header.php'; ?>

  <main class="pt-16">
    <section class="py-16 md:py-24 bg-card border-b border-border">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-foreground mb-4 fade-in-up">Notícias</h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto fade-in-up">Fique por dentro das ações e novidades do CMPCD Jahu</p>
      </div>
    </section>

    <section class="py-16 bg-background">
      <div class="container px-4 max-w-6xl mx-auto">



        <div id="no-results" class="text-center text-muted-foreground py-12 hidden">
          Nenhuma notícia encontrada.
        </div>

        <div id="noticias-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Notícias serão injetadas via JS -->
        </div>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Helper para formatar datas de maneira segura (evitando quebras com campos nulos/vazios)
      function formatarData(dataStr) {
        if (!dataStr || typeof dataStr !== 'string') return '';
        const cleanDate = dataStr.substring(0, 10);
        const parts = cleanDate.split('-');
        if (parts.length === 3) {
          const [year, month, day] = parts;
          return `${day}/${month}/${year}`;
        }
        return cleanDate;
      }

      <?php
      $jsNoticias = [];
      if (!empty($noticias)) {
          foreach ($noticias as $n) {
              $temaMap = [
                  'eventos' => 'Eventos',
                  'direitos' => 'Direitos',
                  'inclusao' => 'Inclusão',
                  'saude' => 'Saúde',
                  'educacao' => 'Educação',
                  'acessibilidade' => 'Acessibilidade',
                  'emprego' => 'Emprego',
                  'informativos' => 'Informativos',
                  'outros' => 'Outros'
              ];
              $categoria = $temaMap[$n['tema']] ?? ucfirst($n['tema']);
              $resumo = mb_strimwidth(strip_tags($n['conteudo']), 0, 150, '...');
              $imagem = $n['imagem_capa'] ? BASE_URL . '/' . $n['imagem_capa'] : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop';
              $jsNoticias[] = [
                  'id' => (string)$n['id'],
                  'titulo' => $n['titulo'],
                  'resumo' => $resumo,
                  'imagem' => $imagem,
                  'categoria' => $categoria,
                  'data' => substr($n['data_publicacao'] ?? $n['criado_em'] ?? '', 0, 10),
                  'conteudo' => $n['conteudo']
              ];
          }
      }
      ?>

      const noticias = <?= !empty($jsNoticias) ? json_encode($jsNoticias) : '[]' ?>;
      
      // Fallback estático caso não haja nenhuma cadastrada ainda
      if (noticias.length === 0) {
        noticias.push(
          {
            id: "1",
            titulo: "CMPCD Jahu realiza audiência pública sobre acessibilidade urbana",
            resumo: "Evento reuniu representantes da sociedade civil e do poder público para discutir melhorias na infraestrutura urbana.",
            imagem: "https://images.unsplash.com/photo-1573497620053-ea5300f94f21?w=800&h=400&fit=crop",
            categoria: "Eventos",
            data: "2026-03-15",
            conteudo: "<p>O Conselho Municipal dos Direitos da Pessoa com Deficiência (CMPCD) de Jahu realizou uma audiência pública no plenário da câmara. O evento reuniu representantes da sociedade civil, arquitetos especializados e do poder público.</p><p>Foram discutidas melhorias fundamentais nas calçadas das regiões comerciais da cidade, incluindo a pavimentação nivelada, implementação correta de piso tátil direcional e de alerta, e rebaixamento de guias para garantir o deslocamento autônomo e seguro de todos.</p>"
          },
          {
            id: "2",
            titulo: "Parceria com APAE amplia atendimento a pessoas com deficiência intelectual",
            resumo: "Convênio firmado entre o conselho e a APAE visa expandir os serviços de reabilitação e inclusão.",
            imagem: "https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=800&h=400&fit=crop",
            categoria: "Inclusão",
            data: "2026-03-10",
            conteudo: "<p>Um novo convênio firmado entre o conselho e a APAE visa expandir os serviços de reabilitação e inclusão no município de Jahu.</p><p>A parceria prevê a ampliação do atendimento a pessoas com deficiência intelectual, com novas vagas para oficinas pedagógicas, atendimento terapêutico e capacitação voltada para o mercado de trabalho local.</p>"
          },
          {
            id: "3",
            titulo: "Campanha de conscientização sobre direitos das pessoas com deficiência",
            resumo: "Ação educativa visa informar a população sobre os direitos garantidos por lei.",
            imagem: "https://images.unsplash.com/photo-1491438590914-bc09fcaaf77a?w=800&h=400&fit=crop",
            categoria: "Direitos",
            data: "2026-02-28",
            conteudo: "<p>A campanha de conscientização promovida pelo CMPCD de Jahu visa informar a população local sobre os direitos garantidos por lei para pessoas com deficiência.</p><p>As ações englobam a distribuição de panfletos informativos em pontos comerciais estratégicos, além de ciclos de palestras em escolas públicas e empresas sobre o respeito às vagas exclusivas de estacionamento e atendimento preferencial.</p>"
          }
        );
      }

      const grid = document.getElementById('noticias-grid');
      const noResults = document.getElementById('no-results');

      const modal = document.getElementById('noticia-modal');
      const modalImg = document.getElementById('modal-img');
      const modalClose = document.getElementById('modal-close');
      const modalCategoria = document.getElementById('modal-categoria');
      const modalData = document.getElementById('modal-data');
      const modalTitulo = document.getElementById('modal-titulo');
      const modalConteudo = document.getElementById('modal-conteudo');

      function openModal(data) {
        modalImg.src = data.imagem || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop';
        modalImg.alt = data.titulo || '';
        modalCategoria.textContent = data.categoria || 'Outros';
        modalData.textContent = formatarData(data.data);
        modalTitulo.textContent = data.titulo || '';
        modalConteudo.innerHTML = data.conteudo || '';
        
        modal.classList.remove('hidden');
        setTimeout(() => {
          modal.classList.remove('opacity-0');
          modal.querySelector('.transform').classList.remove('scale-95');
        }, 10);
      }

      function closeModal() {
        modal.classList.add('opacity-0');
        modal.querySelector('.transform').classList.add('scale-95');
        setTimeout(() => {
          modal.classList.add('hidden');
        }, 300);
      }

      function renderNoticias() {
        grid.innerHTML = '';

        if (noticias.length === 0) {
          grid.classList.add('hidden');
          noResults.classList.remove('hidden');
        } else {
          grid.classList.remove('hidden');
          noResults.classList.add('hidden');

          noticias.forEach(n => {
            const dataStr = formatarData(n.data);
            const article = document.createElement('article');
            article.className = "fade-in-up bg-card rounded-2xl border border-border overflow-hidden hover:-translate-y-1 transition-all duration-300 flex flex-col";
            article.style.boxShadow = "var(--card-shadow)";
            article.innerHTML = `
              <img src="${n.imagem || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop'}" alt="${n.titulo || ''}" class="w-full h-48 object-cover" loading="lazy">
              <div class="p-6 flex flex-col flex-1">
                <span class="inline-block text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full mb-3 align-self-start shrink-0">${n.categoria || 'Outros'}</span>
                <h3 class="font-bold text-foreground mb-2 line-clamp-2">${n.titulo || ''}</h3>
                <p class="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1">${n.resumo || ''}</p>
                <div class="flex items-center justify-between mt-auto pt-2 border-t border-border/50">
                  <span class="text-xs text-muted-foreground flex items-center gap-1">
                    <i data-lucide="calendar" class="w-3 h-3"></i> ${dataStr}
                  </span>
                  <button class="text-sm text-primary font-bold hover:underline inline-flex items-center gap-1 btn-ler-noticia" data-id="${n.id}">
                    Ler mais <i data-lucide="arrow-right" class="w-3 h-3"></i>
                  </button>
                </div>
              </div>
            `;
            grid.appendChild(article);
          });
          if (typeof lucide !== 'undefined') {
            lucide.createIcons();
          }
        }
      }

      document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-ler-noticia');
        if (!btn) return;
        
        e.preventDefault();
        const id = btn.getAttribute('data-id');
        const item = noticias.find(x => x.id == id);
        if (item) {
          openModal(item);
        }
      });

      if (modalClose) modalClose.addEventListener('click', closeModal);
      if (modal) {
        modal.addEventListener('click', (e) => {
          if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
          }
        });
      }

      renderNoticias();
    });
  </script>

  <!-- Modal Notícia -->
  <div id="noticia-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" style="background-color: rgba(0, 0, 0, 0.45);">
    <div class="bg-card rounded-2xl border border-border max-w-2xl w-full overflow-hidden shadow-2xl relative flex flex-col max-h-[85vh] transform scale-95 transition-transform duration-300">
      
      <!-- Fechar (sempre fixo no topo direito do card) -->
      <button id="modal-close" class="absolute top-4 right-4 z-10 p-2 rounded-full bg-background/50 hover:bg-background/80 backdrop-blur-sm text-foreground transition-all shadow-md" aria-label="Fechar">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <!-- Container Rolável -->
      <div class="overflow-y-auto w-full h-full rounded-2xl">
        <!-- Imagem de Capa -->
        <div class="relative h-60 w-full bg-muted">
          <img id="modal-img" src="" alt="Capa" class="w-full h-full object-cover">
        </div>

        <!-- Conteúdo -->
        <div class="p-6 space-y-4">
          <div>
            <span id="modal-categoria" class="inline-block text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full animate-pulse"></span>
            <span id="modal-data" class="text-xs text-muted-foreground ml-3"></span>
          </div>
          <h2 id="modal-titulo" class="text-2xl font-black text-foreground leading-tight"></h2>
          <div id="modal-conteudo" class="text-sm text-muted-foreground leading-relaxed space-y-4 pt-2 border-t border-border">
          </div>
        </div>
      </div>
    </div>
  </div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
