<?php

    require_once __DIR__ . "/Documento.php";

    class Eleicao {
        private int $idEleicao;
        private ?Documento $editalFK; // Referência ao idDocumento
        private string $dataInicioEleicao;
        private string $dataFimEleicao;
        private string $statusEleicao;

        public function __construct(
            string $dataInicioEleicao,
            string $dataFimEleicao,
            string $statusEleicao,
            ?Documento $editalFK = null,
            int $idEleicao = 0
        ) {
            $this->idEleicao = $idEleicao;
            $this->editalFK = $editalFK;
            $this->dataInicioEleicao = $dataInicioEleicao;
            $this->dataFimEleicao = $dataFimEleicao;
            $this->statusEleicao = $statusEleicao;
        }

        public function getIdEleicao() { return $this->idEleicao; }
        public function getEditalFK(): ?Documento { return $this->editalFK; }
        public function getDataInicioEleicao() { return $this->dataInicioEleicao; }
        public function getDataFimEleicao() { return $this->dataFimEleicao; }
        public function getStatusEleicao() { return $this->statusEleicao; }
    }

?>
