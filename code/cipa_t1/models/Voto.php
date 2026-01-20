<?php

    require_once __DIR__ . "/Funcionario.php";

    class Voto {
        private int $idVoto;
        private string $dataHoraVoto;
        private ?Funcionario $funcionarioFK;

        public function __construct(
            string $dataHoraVoto,
            ?Funcionario $funcionarioFK = null,
            int $idVoto = 0
        ) {
            $this->idVoto = $idVoto;
            $this->dataHoraVoto = $dataHoraVoto;
            $this->funcionarioFK = $funcionarioFK;
        }

        public function getIdVoto() { return $this->idVoto; }
        public function getDataHoraVoto() { return $this->dataHoraVoto; }
        public function getFuncionarioFK(): ?Funcionario { return $this->funcionarioFK; }
    }
?>