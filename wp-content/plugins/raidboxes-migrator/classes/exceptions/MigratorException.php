<?php

class MigratorException extends Exception {
    // Die Exception neu definieren, damit die Mitteilung nicht optional ist
    public function __construct($message, $code = 0, Exception $previous = null) {
        // etwas Code

        // sicherstellen, dass alles korrekt zugewiesen wird
        parent::__construct($message, $code, $previous);
    }

    // maßgeschneiderte Stringdarstellung des Objektes
    public function __toString() {
        $error_message = sprintf("%s: Error in %s on Line %s: Code %s \n %s",__CLASS__, $this->file, $this->line, $this->code, $this->message);
        return $error_message;
    }

}