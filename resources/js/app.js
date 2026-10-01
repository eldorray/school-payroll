import "./bootstrap";

import Alpine from "alpinejs";
import { setupDeleteConfirmation } from "./delete-confirmation";
import collapse from "@alpinejs/collapse";

Alpine.plugin(collapse);

window.Alpine = Alpine;

const deleteDialog = document.getElementById('delete-confirmation');
if (deleteDialog) window.confirmDeletion = setupDeleteConfirmation(deleteDialog);

Alpine.start();
