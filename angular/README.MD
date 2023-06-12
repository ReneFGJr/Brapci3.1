Instalar
npm install -g @angular/cli
npm install --save-dev @angular-devkit/build-angular --force
==Removendo versão anterior==
npm uninstall -g @angular/cli
npm cache clean --force
npm cache verify
npm install -g @angular/cli
ng version

Error: Could not find the '@angular-devkit/build-angular:dev-server' builder's node package.
npm install --save-dev @angular-devkit/build-angular --force
