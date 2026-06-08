<?php require_once __DIR__ . '/../partials/admin_header.php'; ?>

<!-- Seção de Estatísticas -->
<div class="flex items-center justify-between border-b border-border pb-4 mb-10">
  <div>
    <h2 class="text-2xl font-black text-foreground">Estatísticas do Município</h2>
    <p class="text-sm text-muted-foreground">Dados consolidados e gráficos sobre as pessoas com deficiência registradas no sistema.</p>
  </div>
</div>

<!-- Stats Cards -->
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
  <div class="rounded-2xl p-6 text-center shadow-lg" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground">
      <?= number_format($stats['total_pcds'], 0, ',', '.') ?>
    </span>
    <p class="text-sm text-primary-foreground/80 font-semibold mt-1">Total de PCDs</p>
  </div>
  <div class="rounded-2xl p-6 text-center shadow-lg" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground">
      <?= number_format($stats['total_bpc'], 0, ',', '.') ?>
    </span>
    <p class="text-sm text-primary-foreground/80 font-semibold mt-1">Recebem BPC/LOAS</p>
  </div>
  <div class="rounded-2xl p-6 text-center shadow-lg" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground">
      <?= number_format($stats['total_tecnologia'], 0, ',', '.') ?>
    </span>
    <p class="text-sm text-primary-foreground/80 font-semibold mt-1">Usam Tec. Assistiva</p>
  </div>
  <div class="rounded-2xl p-6 text-center shadow-lg" style="background: var(--hero-gradient)">
    <span class="text-3xl md:text-4xl font-black text-primary-foreground">
      <?= number_format($stats['total_entidades'], 0, ',', '.') ?>
    </span>
    <p class="text-sm text-primary-foreground/80 font-semibold mt-1">Vinculados a Entidades</p>
  </div>
</div>

<!-- Gráficos -->
<div class="grid lg:grid-cols-2 gap-8 mb-12">
  <!-- Tipo de Deficiência (Pie Chart) -->
  <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
    <h3 class="text-lg font-bold text-foreground mb-4">Tipo de Deficiência</h3>
    <div style="position: relative; height:300px; width:100%">
      <canvas id="chartTipo"></canvas>
    </div>
  </div>

  <!-- Faixa Etária (Bar Chart) -->
  <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
    <h3 class="text-lg font-bold text-foreground mb-4">Faixa Etária</h3>
    <div style="position: relative; height:300px; width:100%">
      <canvas id="chartIdade"></canvas>
    </div>
  </div>

  <!-- Escolaridade (Horizontal Bar Chart) -->
  <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
    <h3 class="text-lg font-bold text-foreground mb-4">Escolaridade</h3>
    <div style="position: relative; height:300px; width:100%">
      <canvas id="chartEscolaridade"></canvas>
    </div>
  </div>

  <!-- Por Bairro (Bar Chart) -->
  <div class="bg-card rounded-2xl p-6 border border-border" style="box-shadow: var(--card-shadow)">
    <h3 class="text-lg font-bold text-foreground mb-4">Por Bairro/Região</h3>
    <div style="position: relative; height:300px; width:100%">
      <canvas id="chartBairro"></canvas>
    </div>
  </div>
</div>

<!-- Disclaimer LGPD -->
<div class="bg-card rounded-2xl p-6 border border-border text-center" style="box-shadow: var(--card-shadow)">
  <p class="text-sm text-muted-foreground">
    <strong>Nota:</strong> Todos os dados estatísticos são anonimizados e apresentados de forma agregada, em conformidade com a LGPD (Lei Geral de Proteção de Dados). Nenhuma informação pessoal identificável é exposta nos painéis.
  </p>
</div>

<?php
// Prepara os arrays para o JS de tipos de deficiências
$defLabels = array_column($stats['deficiencias'], 'nome');
$defData = array_map('intval', array_column($stats['deficiencias'], 'total'));

// Prepara faixa etária
$ageLabels = array_keys($stats['idades']);
$ageData = array_map('intval', array_values($stats['idades']));

// Prepara escolaridade
$schoolLabels = array_keys($stats['escolaridade']);
$schoolData = array_map('intval', array_values($stats['escolaridade']));

// Prepara bairros
$bairroLabels = array_keys($stats['bairros']);
$bairroData = array_map('intval', array_values($stats['bairros']));
?>

<!-- Lógica dos Gráficos com Chart.js -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // 1. Tipo de Deficiência
    const defLabels = <?= json_encode($defLabels) ?>;
    const defData = <?= json_encode($defData) ?>;
    
    new Chart(document.getElementById('chartTipo'), {
      type: 'doughnut',
      data: {
        labels: defLabels.length > 0 ? defLabels : ['Nenhum cadastro'],
        datasets: [{
          data: defData.length > 0 ? defData : [0],
          backgroundColor: ['#2D9CDB', '#56CCF2', '#F2994A', '#6FCF97', '#BB6BD9', '#EB5757', '#64748b'],
          borderWidth: 0
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              padding: 15
            }
          }
        }
      }
    });

    // 2. Faixa Etária
    const ageLabels = <?= json_encode($ageLabels) ?>;
    const ageData = <?= json_encode($ageData) ?>;

    new Chart(document.getElementById('chartIdade'), {
      type: 'bar',
      data: {
        labels: ageLabels,
        datasets: [{
          label: 'Pessoas',
          data: ageData,
          backgroundColor: 'hsl(204, 67%, 52%)',
          borderRadius: 4
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // 3. Escolaridade
    const schoolLabels = <?= json_encode($schoolLabels) ?>;
    const schoolData = <?= json_encode($schoolData) ?>;

    new Chart(document.getElementById('chartEscolaridade'), {
      type: 'bar',
      data: {
        labels: schoolLabels,
        datasets: [{
          label: 'Pessoas',
          data: schoolData,
          backgroundColor: 'hsl(195, 84%, 64%)',
          borderRadius: 4
        }]
      },
      options: { 
        indexAxis: 'y',
        responsive: true, 
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // 4. Por Bairro
    const bairroLabels = <?= json_encode($bairroLabels) ?>;
    const bairroData = <?= json_encode($bairroData) ?>;

    new Chart(document.getElementById('chartBairro'), {
      type: 'bar',
      data: {
        labels: bairroLabels.length > 0 ? bairroLabels : ['Nenhum cadastro'],
        datasets: [{
          label: 'Pessoas',
          data: bairroData.length > 0 ? bairroData : [0],
          backgroundColor: 'hsl(204, 67%, 52%)',
          borderRadius: 4
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });
  });
</script>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
