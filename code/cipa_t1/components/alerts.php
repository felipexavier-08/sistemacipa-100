<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<!-- Sistema de Alerts -->
<div id="alerts-container" class="alerts-container">
    <?php if (isset($_SESSION['sucesso_funcionario'])): ?>
        <div class="alert alert-success" data-type="success">
            <span class="alert-icon">✅</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['sucesso_funcionario']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['sucesso_funcionario']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro_funcionario'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro_funcionario']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro_funcionario']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso_candidato'])): ?>
        <div class="alert alert-success" data-type="success">
            <span class="alert-icon">✅</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['sucesso_candidato']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['sucesso_candidato']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro_candidato'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro_candidato']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro_candidato']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso_eleicao'])): ?>
        <div class="alert alert-success" data-type="success">
            <span class="alert-icon">✅</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['sucesso_eleicao']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['sucesso_eleicao']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro_eleicao'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro_eleicao']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro_eleicao']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso_documento'])): ?>
        <div class="alert alert-success" data-type="success">
            <span class="alert-icon">✅</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['sucesso_documento']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['sucesso_documento']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro_documento'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro_documento']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro_documento']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso_voto'])): ?>
        <div class="alert alert-success" data-type="success">
            <span class="alert-icon">✅</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['sucesso_voto']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['sucesso_voto']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro_voto'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro_voto']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro_voto']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro_ata'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro_ata']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro_ata']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso_ata'])): ?>
        <div class="alert alert-success" data-type="success">
            <span class="alert-icon">✅</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['sucesso_ata']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['sucesso_ata']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro_login'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro_login']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro_login']); ?>
    <?php endif; ?>

    <!-- Alertas genéricos para qualquer outra operação -->
    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success" data-type="success">
            <span class="alert-icon">✅</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['sucesso']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-error" data-type="error">
            <span class="alert-icon">❌</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['erro']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['info'])): ?>
        <div class="alert alert-info" data-type="info">
            <span class="alert-icon">ℹ️</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['info']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['info']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning" data-type="warning">
            <span class="alert-icon">⚠️</span>
            <span class="alert-message"><?php echo htmlspecialchars($_SESSION['warning']); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>
</div>

<script>
// Auto-remove alerts após 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentElement) {
                        alert.remove();
                    }
                }, 300);
            }
        }, 5000);
    });
});
</script>
