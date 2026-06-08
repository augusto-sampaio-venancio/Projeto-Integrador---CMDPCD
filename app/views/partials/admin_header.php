<?php
$unreadContatoModel = new Contato();
$unreadMessagesCount = $unreadContatoModel->getUnreadCount();

$pcdModelForHeader = new Pcd();
$pendingPcdsCount = $pcdModelForHeader->getPendingCount();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Administrativo - CMDPCD Jahu</title>
  <meta name="description" content="Área restrita de gerenciamento.">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Chart.js para gráficos se necessário -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-background text-foreground font-sans">
  
  <header class="fixed top-0 left-0 right-0 z-50 bg-card/95 backdrop-blur-md border-b border-border">
    <div class="container mx-auto px-4 flex items-center justify-between h-16">
      <a href="<?= BASE_URL ?>/" class="flex items-center gap-2 font-extrabold text-xl text-primary">
        <i data-lucide="accessibility" class="w-7 h-7"></i>
        <span>CMDPCD Jahu</span>
      </a>
      <nav class="hidden lg:flex items-center gap-6" aria-label="Navegação principal">
        <a href="<?= BASE_URL ?>/" class="text-sm font-semibold text-foreground/70 hover:text-primary transition-colors">Início</a>
        <a href="<?= BASE_URL ?>/quem-somos" class="text-sm font-semibold text-foreground/70 hover:text-primary transition-colors">Quem Somos</a>
        <a href="<?= BASE_URL ?>/iniciativas" class="text-sm font-semibold text-foreground/70 hover:text-primary transition-colors">Iniciativas</a>
        <a href="<?= BASE_URL ?>/noticias" class="text-sm font-semibold text-foreground/70 hover:text-primary transition-colors">Notícias</a>
        <a href="<?= BASE_URL ?>/transparencia" class="text-sm font-semibold text-foreground/70 hover:text-primary transition-colors">Transparência</a>
        <a href="<?= BASE_URL ?>/apoio" class="text-sm font-semibold text-foreground/70 hover:text-primary transition-colors">Apoio</a>
        <a href="<?= BASE_URL ?>/contato" class="text-sm font-semibold text-foreground/70 hover:text-primary transition-colors">Contato</a>
        <a href="<?= BASE_URL ?>/cadastro-pcd" class="text-sm font-bold bg-primary text-primary-foreground px-4 py-2 rounded-full hover:bg-primary/90 transition-colors">Registrar PCD</a>
      </nav>
    </div>
  </header>

  <main class="pt-16 min-h-screen flex flex-col">
    <div class="flex-1 flex flex-col">
      <section class="py-12 bg-card border-b border-border">
        <div class="container mx-auto px-4 text-center">
          <h1 class="text-3xl font-black text-foreground mb-2">Painel Administrativo</h1>
          <p class="text-muted-foreground">Gerenciamento do sistema</p>
        </div>
      </section>

      <section class="py-12 bg-background flex-1">
        <div class="container mx-auto px-4 max-w-6xl">
          
          <!-- Menu do Dashboard / Abas -->
          <div class="flex flex-wrap items-center justify-between gap-4 mb-12">
            <div class="flex items-center gap-3">
              <span class="text-sm text-muted-foreground">
                Olá, <strong class="text-foreground"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrador') ?></strong>
              </span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
              <a href="<?= BASE_URL ?>/admin/dashboard" class="px-4 py-2 text-sm font-semibold rounded-full flex items-center gap-2 transition-colors <?= ($activeTab === 'dashboard') ? 'bg-primary text-primary-foreground' : 'bg-card text-foreground border border-border hover:border-primary/30' ?>">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Dashboard
              </a>
              <?php if (!isEditor()): ?>
                <a href="<?= BASE_URL ?>/admin/pcds" class="px-4 py-2 text-sm font-semibold rounded-full flex items-center gap-2 transition-colors <?= ($activeTab === 'cadastros') ? 'bg-primary text-primary-foreground' : 'bg-card text-foreground border border-border hover:border-primary/30' ?>">
                  <i data-lucide="clipboard-list" class="w-4 h-4"></i> Cadastrados
                  <?php if ($pendingPcdsCount > 0): ?>
                    <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-destructive rounded-full shrink-0 animate-pulse">
                      <?= $pendingPcdsCount ?>
                    </span>
                  <?php endif; ?>
                </a>
                <a href="<?= BASE_URL ?>/admin/usuarios" class="px-4 py-2 text-sm font-semibold rounded-full flex items-center gap-2 transition-colors <?= ($activeTab === 'users') ? 'bg-primary text-primary-foreground' : 'bg-card text-foreground border border-border hover:border-primary/30' ?>">
                  <i data-lucide="users" class="w-4 h-4"></i> Usuários
                </a>
              <?php endif; ?>
              <a href="<?= BASE_URL ?>/admin/conteudos" class="px-4 py-2 text-sm font-semibold rounded-full flex items-center gap-2 transition-colors <?= ($activeTab === 'conteudos') ? 'bg-primary text-primary-foreground' : 'bg-card text-foreground border border-border hover:border-primary/30' ?>">
                <i data-lucide="file-text" class="w-4 h-4"></i> Conteúdos
              </a>
              <?php if (!isEditor()): ?>
                <a href="<?= BASE_URL ?>/admin/mensagens" class="px-4 py-2 text-sm font-semibold rounded-full flex items-center gap-2 transition-colors <?= ($activeTab === 'mensagens') ? 'bg-primary text-primary-foreground' : 'bg-card text-foreground border border-border hover:border-primary/30' ?>">
                  <i data-lucide="mail" class="w-4 h-4"></i> Mensagens
                  <?php if ($unreadMessagesCount > 0): ?>
                    <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-destructive rounded-full shrink-0">
                      <?= $unreadMessagesCount ?>
                    </span>
                  <?php endif; ?>
                </a>
              <?php endif; ?>
              <a href="<?= BASE_URL ?>/admin/estatisticas" class="px-4 py-2 text-sm font-semibold rounded-full flex items-center gap-2 transition-colors <?= ($activeTab === 'estatisticas') ? 'bg-primary text-primary-foreground' : 'bg-card text-foreground border border-border hover:border-primary/30' ?>">
                <i data-lucide="pie-chart" class="w-4 h-4"></i> Estatísticas
              </a>
              <a href="<?= BASE_URL ?>/admin/logout" class="bg-card text-foreground border border-border px-4 py-2 text-sm font-semibold rounded-full hover:bg-destructive/10 hover:text-destructive transition-colors hover:border-destructive/30 flex items-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i> Sair
              </a>
            </div>
          </div>
          
          <!-- Início do conteúdo da página específica -->
