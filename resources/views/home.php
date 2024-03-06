<main class="container principal">
    <div class="row">
        <?php foreach ($videos as $key => $video): ?>
            <div class="video p-2 col-12 col-sm-6 col-md-4">
                <a href="https://www.youtube.com/watch?v=<?= $video['key'] ?>" target="_blank">
                    <img class="img-fluid" src="https://i.ytimg.com/vi/<?=$video['key']?>/sddefault.jpg" alt="<?= $video['title'] ?>">
                </a>
                <h1 class="py-2 mt-2 mb-0 tituloMusicas">
                    <span class="<?php echo $classes[$key + 1] ?? ''; ?>"><?=$key + 1?>°</span><?=$video['title']?>
                </h1>
                <p class="lead visualizacoes">
                    <span class="bordaVisualizacoes">10000000000</span> visualizações
                </p>
                <p class="lead"><span class="numero">10</span> dia(s) pra chegar em <?=$video['milharNumero']?> visualizações</p>
            </div>
        <?php endforeach; ?>
    </div>
</main>
