<?php require_once __DIR__ . '/../../partials/admin_header.php'; ?>

<!-- ABA: Usuários -->
<div id="tab-users" class="admin-tab-content">
  
  <?php if (!empty($success)): ?>
    <div class="bg-green-500/10 text-green-600 text-sm p-4 rounded-xl border border-green-500/20 mb-6 flex items-center gap-2">
      <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($success) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="bg-destructive/10 text-destructive text-sm p-4 rounded-xl border border-destructive/20 mb-6 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <div class="bg-card rounded-2xl border border-border overflow-hidden" style="box-shadow: var(--card-shadow)">
    <div class="p-6 border-b border-border flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-xl font-bold">Usuários Administrativos</h2>
        <p class="text-sm text-muted-foreground">Gerencie quem tem acesso ao painel.</p>
      </div>
      <?php if (!isAdminParcial()): ?>
        <a href="<?= BASE_URL ?>/admin/usuarios/create" class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-primary/90 transition-colors">
          <i data-lucide="plus" class="w-4 h-4"></i> Novo Usuário
        </a>
      <?php endif; ?>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-xs text-muted-foreground uppercase bg-muted/50">
          <tr>
            <th class="px-6 py-4 font-bold">Nome</th>
            <th class="px-6 py-4 font-bold">E-mail</th>
            <th class="px-6 py-4 font-bold">CPF</th>
            <th class="px-6 py-4 font-bold">Celular</th>
            <th class="px-6 py-4 font-bold">Papel</th>
            <th class="px-6 py-4 font-bold">Status</th>
            <th class="px-6 py-4 font-bold text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <?php if (count($usuarios) === 0): ?>
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                Nenhum usuário administrativo encontrado.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($usuarios as $user): ?>
              <?php 
                $roleName = 'Editor';
                if ($user['perfil_nome'] === 'admin_total') $roleName = 'Admin Total';
                elseif ($user['perfil_nome'] === 'admin_parcial') $roleName = 'Admin Parcial';
                
                // Formata CPF
                $cpf = $user['cpf'];
                if (strlen($cpf) === 11) {
                  $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9);
                }

                // Formata Celular
                $cel = $user['celular'];
                if (strlen($cel) === 11) {
                  $cel = '(' . substr($cel, 0, 2) . ') ' . substr($cel, 2, 5) . '-' . substr($cel, 7);
                }
              ?>
              <tr class="hover:bg-muted/50 transition-colors">
                <td class="px-6 py-4 font-semibold text-foreground flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                    <?= strtoupper(substr($user['nome_completo'], 0, 1)) ?>
                  </div>
                  <span><?= htmlspecialchars($user['nome_completo']) ?></span>
                </td>
                <td class="px-6 py-4 text-muted-foreground"><?= htmlspecialchars($user['email']) ?></td>
                <td class="px-6 py-4 text-muted-foreground"><?= htmlspecialchars($cpf) ?></td>
                <td class="px-6 py-4 text-muted-foreground"><?= htmlspecialchars($cel) ?></td>
                <td class="px-6 py-4">
                  <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full">
                    <?= $roleName ?>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <?php if ($user['status'] === 'ativo'): ?>
                    <span class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="check" class="w-3 h-3"></i> Ativo
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 bg-destructive/10 text-destructive text-xs font-bold px-2.5 py-1 rounded-full">
                      <i data-lucide="x" class="w-3 h-3"></i> <?= htmlspecialchars(ucfirst($user['status'])) ?>
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <a href="<?= BASE_URL ?>/admin/usuarios/edit?id=<?= $user['id'] ?>" class="p-2 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" title="<?= isAdminParcial() ? 'Visualizar' : 'Editar' ?>">
                      <i data-lucide="<?= isAdminParcial() ? 'eye' : 'edit' ?>" class="w-4 h-4"></i>
                    </a>
                    
                    <?php if (!isAdminParcial()): ?>
                      <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <a href="<?= BASE_URL ?>/admin/usuarios/delete?id=<?= $user['id'] ?>" onclick="return confirm('Deseja realmente excluir o usuário <?= htmlspecialchars($user['nome_completo']) ?>?');" class="p-2 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors" title="Excluir">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </a>
                      <?php else: ?>
                        <span class="p-2 opacity-30 cursor-not-allowed" title="Você não pode se excluir">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </span>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../partials/admin_footer.php'; ?>
