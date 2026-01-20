<?php

    require_once __DIR__ . "/Eleicao.php";

    class BrancoOuNulo {
        private int $idBrancoNulo;
        private int $quantidadeBranco;
        private int $quantidadeNulo;
        private ?Eleicao $eleicaoFK;

        public function __construct(
            int $quantidadeBranco,
            int $quantidadeNulo,
            ?Eleicao $eleicaoFK = null,
            int $idBrancoNulo = 0
        ) {
            $this->idBrancoNulo = $idBrancoNulo;
            $this->quantidadeBranco = $quantidadeBranco;
            $this->quantidadeNulo = $quantidadeNulo;
            $this->eleicaoFK = $eleicaoFK;
        }

        public function getIdBrancoNulo() { return $this->idBrancoNulo; }
        public function getQuantidadeBranco() { return $this->quantidadeBranco; } 
        public function getQuantidadeNulo() { return $this->quantidadeNulo; } 
        public function getEleicaoFK(): Eleicao { return $this->eleicaoFK; } 
    }
?>