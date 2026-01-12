<? global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']); ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="row">
    <? //echo $_SESSION['userData']['default_language'] ; ?>
    <div class="col-sm-12">
        <div class="tile">
            <div class="tile-body">
                <div class="d-flex justify-content-center">
                    <div class="card" style="width: 80%; background:transparent; border:none;">
                        <ul class="list-group list-group-flush" style="flex-direction:row; align-items:center; justify-content:space-between;">
                            <? if($dataP['totalFinished']<33){
                                $size = '40px';
                                $colorT = 'var(--color9)';
                            }else if($dataP['totalFinished']>32 && $dataP['totalFinished']<66){
                                $size = '60px';
                                $colorT = 'var(--color7)';
                            }else{
                                $size = '80px';
                                $colorT = 'var(--color8)';
                            } ?>
                            <div style="display:flex; flex-direction:column; align-items:center;">
                                <h2><?=$fnT('Total de oportunidades')?>: <?=$dataP['totalOpps']?></h2>
                                <b><span style="font-size:<?=$size?>; color:<?=$colorT?>"><?=$totalFinished?></span><span style="font-size:60px;">/<?=$dataP['totalOpps']?></span></b>
                            </div>
                            <canvas id="myPieChart"></canvas>
                                            <script>

                                                dataG = {
                                                            labels: [fnT('Pendente'), fnT('Em revisão'), fnT('Concluído')],
                                                            datasets: [{
                                                                data: [0, 0, 0],
                                                                backgroundColor: [
                                                                    color6v, // Pendiente
                                                                    color7v, // En proceso
                                                                    color8v // Completadas
                                                                ],
                                                                borderColor: '#fff',
                                                                borderWidth: 0,
                                                                hoverOffset: 15
                                                            }]
                                                        };

                                                        // Configuración
                                                        config = {
                                                            type: 'doughnut',
                                                            data: dataG,
                                                            options: {
                                                                responsive: true,
                                                                plugins: {
                                                                    legend: {
                                                                        position: 'top',
                                                                    },
                                                                    title: {
                                                                        display: true
                                                                    },
                                                                    tooltip: {
                                                                        callbacks: {
                                                                            label: function(context) {
                                                                                let label = context.label || '';
                                                                                let value = context.raw || 0;
                                                                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                                                let percentage = Math.round((value / total) * 100);
                                                                                return `${label}: ${value} (${percentage}%)`;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        };

                                                        // Crear la gráfica
                                                        ctx = document.getElementById('myPieChart').getContext('2d');
                                                        chart = new Chart(ctx, config);
                                                    
                                                        chart.data.datasets[0].data = [<?=$totalPending?>, <?=$totalReview?>, <?=$totalFinished?>];
                                                        chart.update();
                                            </script>
                            <!--<li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-warning text-dark font-weight-bold" role="progressbar" style="width: <?=$dataP['totalPending']?>%" aria-valuenow="<?=$dataP['totalPending']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalPending']?>% <?= $fnT('Pendente') ?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-info text-dark font-weight-bold" role="progressbar" style="width: <?=$dataP['totalReview']?>%" aria-valuenow="<?=$dataP['totalReview']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalReview']?>% <?= $fnT('Em processo') ?></div>
                                </div>
                            </li>-->
                            <!--<li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?=$dataP['totalApproved']?>%" aria-valuenow="<?=$dataP['totalApproved']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalApproved']?>% Rejected</div>
                                </div>
                            </li>-->
                            <!--<li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-success text-dark font-weight-bold" role="progressbar" style="width: <?=$dataP['totalFinished']?>%" aria-valuenow="<?=$dataP['totalFinished']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalFinished']?>% <?= $fnT('Finalizado') ?></div>
                                </div>
                            </li>-->
                            <!--<li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?=$dataP['totalRejected']?>%" aria-valuenow="<?=$dataP['totalRejected']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalRejected']?>% Rejected</div>
                                </div>
                            </li>-->
                            <!--<li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-primary text-dark font-weight-bold" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"><?= $fnT('Total de oportunidades') ?> (<?=$dataP['totalOpps']?>)</div>
                                </div>
                            </li>-->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>