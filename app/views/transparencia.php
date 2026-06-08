<?php require_once __DIR__ . '/partials/header.php'; ?>

  <main class="pt-16">
    <section class="py-16 md:py-24 bg-card border-b border-border">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-5xl font-black text-foreground mb-4 fade-in-up">Transparência</h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto fade-in-up">Acesso a documentos, atas, resoluções e informações públicas do conselho</p>
      </div>
    </section>

    <!-- Documentos -->
    <section class="py-16 bg-background">
      <div class="container mx-auto px-4 max-w-5xl">
        <h2 class="text-2xl font-extrabold text-foreground mb-8 fade-in-up">Documentos e Publicações</h2>
        <div class="space-y-4">
          <?php if (!empty($documentos)): ?>
            <?php foreach ($documentos as $doc): ?>
              <?php
                $tipoMap = [
                  'ata' => 'Ata',
                  'resolucao' => 'Resolução',
                  'edital' => 'Edital',
                  'oficio' => 'Ofício',
                  'relatorio' => 'Relatório',
                  'outros' => 'Documento'
                ];
                $iconMap = [
                  'ata' => 'file-text',
                  'resolucao' => 'book-open',
                  'edital' => 'clipboard-list',
                  'oficio' => 'mail',
                  'relatorio' => 'folder-open',
                  'outros' => 'globe'
                ];
                $tipoLabel = $tipoMap[$doc['tipo']] ?? 'Documento';
                $icon = $iconMap[$doc['tipo']] ?? 'file-text';
                $dataStr = date('d/m/Y', strtotime($doc['data_publicacao']));
                $fileUrl = BASE_URL . '/' . $doc['caminho_arquivo'];
              ?>
              <div class="flex items-center gap-4 bg-card rounded-xl p-4 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                  <i data-lucide="<?= $icon ?>" class="w-5 h-5 text-primary"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <span class="text-xs font-bold text-primary"><?= $tipoLabel ?></span>
                  <h3 class="font-semibold text-foreground text-sm truncate" title="<?= htmlspecialchars($doc['titulo']) ?>"><?= htmlspecialchars($doc['titulo']) ?></h3>
                  <?php if ($doc['descricao']): ?>
                    <p class="text-xs text-muted-foreground line-clamp-1 mt-0.5"><?= htmlspecialchars($doc['descricao']) ?></p>
                  <?php endif; ?>
                  <span class="text-xs text-muted-foreground flex items-center gap-1 mt-1">
                    <i data-lucide="calendar" class="w-3 h-3"></i> <?= $dataStr ?>
                  </span>
                </div>
                <a href="<?= $fileUrl ?>" target="_blank" class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors" aria-label="Baixar">
                  <i data-lucide="download" class="w-5 h-5"></i>
                </a>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fallback estático se o banco de dados estiver vazio -->
            <!-- Documento 1 -->
            <div class="flex items-center gap-4 bg-card rounded-xl p-4 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold text-primary">Ata</span>
                <h3 class="font-semibold text-foreground text-sm truncate">Ata da Reunião Ordinária - Março 2025</h3>
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <i data-lucide="calendar" class="w-3 h-3"></i> 15/03/2025
                </span>
              </div>
              <a href="#" class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors" aria-label="Baixar">
                <i data-lucide="download" class="w-5 h-5"></i>
              </a>
            </div>
            <!-- Documento 2 -->
            <div class="flex items-center gap-4 bg-card rounded-xl p-4 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold text-primary">Ata</span>
                <h3 class="font-semibold text-foreground text-sm truncate">Ata da Reunião Extraordinária - Fevereiro 2025</h3>
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <i data-lucide="calendar" class="w-3 h-3"></i> 20/02/2025
                </span>
              </div>
              <a href="#" class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors" aria-label="Baixar">
                <i data-lucide="download" class="w-5 h-5"></i>
              </a>
            </div>
            <!-- Documento 3 -->
            <div class="flex items-center gap-4 bg-card rounded-xl p-4 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold text-primary">Resolução</span>
                <h3 class="font-semibold text-foreground text-sm truncate">Resolução nº 05/2025 - Normas de Acessibilidade</h3>
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <i data-lucide="calendar" class="w-3 h-3"></i> 01/03/2025
                </span>
              </div>
              <a href="#" class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors" aria-label="Baixar">
                <i data-lucide="download" class="w-5 h-5"></i>
              </a>
            </div>
            <!-- Documento 4 -->
            <div class="flex items-center gap-4 bg-card rounded-xl p-4 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold text-primary">Resolução</span>
                <h3 class="font-semibold text-foreground text-sm truncate">Resolução nº 04/2025 - Programa de Inclusão</h3>
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <i data-lucide="calendar" class="w-3 h-3"></i> 15/02/2025
                </span>
              </div>
              <a href="#" class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors" aria-label="Baixar">
                <i data-lucide="download" class="w-5 h-5"></i>
              </a>
            </div>
            <!-- Documento 5 -->
            <div class="flex items-center gap-4 bg-card rounded-xl p-4 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i data-lucide="folder-open" class="w-5 h-5 text-primary"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold text-primary">Documento</span>
                <h3 class="font-semibold text-foreground text-sm truncate">Regimento Interno do CMPCD</h3>
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <i data-lucide="calendar" class="w-3 h-3"></i> 01/06/2024
                </span>
              </div>
              <a href="#" class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors" aria-label="Baixar">
                <i data-lucide="download" class="w-5 h-5"></i>
              </a>
            </div>
            <!-- Documento 6 -->
            <div class="flex items-center gap-4 bg-card rounded-xl p-4 border border-border hover:border-primary/30 transition-all fade-in-up" style="box-shadow: var(--card-shadow)">
              <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i data-lucide="globe" class="w-5 h-5 text-primary"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold text-primary">Informe</span>
                <h3 class="font-semibold text-foreground text-sm truncate">Relatório Anual de Atividades 2024</h3>
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <i data-lucide="calendar" class="w-3 h-3"></i> 30/01/2025
                </span>
              </div>
              <a href="#" class="shrink-0 w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary hover:bg-primary/20 transition-colors" aria-label="Baixar">
                <i data-lucide="download" class="w-5 h-5"></i>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Conselheiros -->
    <section class="py-16 bg-card">
      <div class="container mx-auto px-4 max-w-5xl">
        <h2 class="text-2xl font-extrabold text-foreground mb-8 flex items-center gap-2 fade-in-up">
          <i data-lucide="users" class="w-6 h-6 text-primary"></i> Conselheiros
        </h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php if (!empty($membrosAtuais)): ?>
            <?php foreach ($membrosAtuais as $membro): ?>
              <?php
                $funcoesMap = [
                  'presidente' => 'Presidente',
                  'vice-presidente' => 'Vice-Presidente',
                  'secretario' => 'Secretário(a)',
                  'titular' => 'Conselheiro(a) Titular',
                  'suplente' => 'Conselheiro(a) Suplente'
                ];
                $funcao = $funcoesMap[$membro['funcao']] ?? ucfirst($membro['funcao']);
                $periodo = date('Y', strtotime($membro['data_inicio']));
                if ($membro['data_fim']) {
                  $periodo .= '-' . date('Y', strtotime($membro['data_fim']));
                } else {
                  $periodo .= '-Atual';
                }
              ?>
              <div class="bg-background rounded-xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
                <h4 class="font-bold text-foreground"><?= htmlspecialchars($membro['nome_completo']) ?></h4>
                <p class="text-sm text-muted-foreground mt-1"><?= $membro['entidade_representada'] ? htmlspecialchars($membro['entidade_representada']) : 'Poder Público' ?></p>
                <p class="text-xs text-primary font-semibold mt-2">Mandato: <?= $periodo ?> (<?= $funcao ?>)</p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fallback estático se o banco de dados estiver vazio -->
            <div class="bg-background rounded-xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground">Maria Silva</h4>
              <p class="text-sm text-muted-foreground mt-1">Poder Público - Assistência Social</p>
              <p class="text-xs text-primary font-semibold mt-2">Mandato: 2024-2026</p>
            </div>
            <div class="bg-background rounded-xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground">João Santos</h4>
              <p class="text-sm text-muted-foreground mt-1">Poder Público - Saúde</p>
              <p class="text-xs text-primary font-semibold mt-2">Mandato: 2024-2026</p>
            </div>
            <div class="bg-background rounded-xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground">Ana Costa</h4>
              <p class="text-sm text-muted-foreground mt-1">Sociedade Civil - APAE</p>
              <p class="text-xs text-primary font-semibold mt-2">Mandato: 2024-2026</p>
            </div>
            <div class="bg-background rounded-xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground">Carlos Oliveira</h4>
              <p class="text-sm text-muted-foreground mt-1">Sociedade Civil - AMAE</p>
              <p class="text-xs text-primary font-semibold mt-2">Mandato: 2024-2026</p>
            </div>
            <div class="bg-background rounded-xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground">Lucia Pereira</h4>
              <p class="text-sm text-muted-foreground mt-1">Poder Público - Educação</p>
              <p class="text-xs text-primary font-semibold mt-2">Mandato: 2024-2026</p>
            </div>
            <div class="bg-background rounded-xl p-5 border border-border fade-in-up" style="box-shadow: var(--card-shadow)">
              <h4 class="font-bold text-foreground">Roberto Lima</h4>
              <p class="text-sm text-muted-foreground mt-1">Sociedade Civil - CISC</p>
              <p class="text-xs text-primary font-semibold mt-2">Mandato: 2024-2026</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Informações institucionais -->
    <section class="py-16 bg-background">
      <div class="container mx-auto px-4 max-w-3xl">
        <h2 class="text-2xl font-extrabold text-foreground mb-8 fade-in-up">Informações Institucionais</h2>
        <div class="bg-card rounded-2xl p-8 border border-border space-y-4 fade-in-up" style="box-shadow: var(--card-shadow)">
          <div class="flex items-center gap-3"><i data-lucide="map-pin" class="w-5 h-5 text-primary shrink-0"></i><span class="text-foreground">Rua Exemplo, 123 - Centro, Jahu - SP, CEP 17201-000</span></div>
          <div class="flex items-center gap-3"><i data-lucide="phone" class="w-5 h-5 text-primary shrink-0"></i><span class="text-foreground">(14) 3622-0000</span></div>
          <div class="flex items-center gap-3"><i data-lucide="mail" class="w-5 h-5 text-primary shrink-0"></i><span class="text-foreground">cmpcd@jau.sp.gov.br</span></div>
        </div>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
