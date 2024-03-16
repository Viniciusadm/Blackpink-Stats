<?php
/** @var array $videos */
/** @var array $classes */
/** @var bool $admin */
?>

<header class="container">
    <div class="d-flex justify-content-between mt-2">
        <img class="iconeSite img-fluid py-2" src="/resources/images/icone.svg" alt="icone">
        <label class="d-block mt-2 mb-2">
            <select id="ordenar" class="form-control">
                <option value="views">Visualizações</option>
                <option value="days">Dias para</option>
                <option value="media">Média</option>
                <option value="published">Publicação</option>
            </select>
        </label>
    </div>
</header>
<main class="container principal">
    <div class="row" id="videos">
        <?php foreach ($videos as $key => $video): ?>
            <div
                class="video p-2 pb-3 col-12 col-sm-6 col-md-4"
                data-views="<?=$video->views?>"
                data-days="<?=$video->days_to?>"
                data-media="<?=$video->media?>"
                data-published="<?=strtotime($video->published_at)?>"
            >
                <a href="https://www.youtube.com/watch?v=<?= $video->key ?>" target="_blank">
                    <img class="img-fluid" src="https://i.ytimg.com/vi/<?=$video->key?>/sddefault.jpg" alt="<?= $video->title ?>">
                </a>
                <a class="d-block py-2 mt-2 mb-0 tituloMusicas" href="/details/<?=$video->slug?>">
                    <span class="<?php echo $classes[$key + 1] ?? ''; ?>"><?=$key + 1?>°</span> <?=$video->title?>
                </a>
                <p class="lead visualizacoes">
                    <span
                        class="bordaVisualizacoes"
                        title="Em média <?=$video->formatNumber('media')?> visualizações por dia"
                    >
                        <?=$video->formatNumber('views')?></span> visualizações
                </p>
                <p class="lead mb-0">
                    <span class="numero" title="Publicado em <?=$video->formatDate('published_at')?>">
                        <?=$video->days_to?>
                    </span> dia(s) pra chegar em <?=$video->formatNumber('next')?> visualizações
                </p>
                <?php
                    if ($admin) {
                        echo '<p class="mb-0">Taxa de decaimento: ' . $video->formatNumber('decay_rate') . '</p>';
                    }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script src="/resources/js/home.js"></script>
