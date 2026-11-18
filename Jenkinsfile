pipeline {
    agent any

    environment {
        DOCKERHUB_USER  = 'rafliriandraa'
        IMAGE_NAME      = "${DOCKERHUB_USER}/antrisehat"
        IMAGE_TAG       = "${env.BUILD_NUMBER}"
        DOCKERHUB_CREDS = 'dockerhub-creds'
    }

    stages {
        stage('1. Checkout') {
            steps {
                checkout scm
            }
        }

        stage('2. Build') {
            steps {
                bat 'docker build --target production -t %IMAGE_NAME%:%IMAGE_TAG% -t %IMAGE_NAME%:latest .'
            }
        }

        stage('3. Test') {
            steps {
                bat 'docker build --target test -t antrisehat-test:%IMAGE_TAG% .'
                bat 'docker run --rm antrisehat-test:%IMAGE_TAG%'
            }
        }

        stage('4. Push Image') {
            steps {
                withCredentials([usernamePassword(credentialsId: "${DOCKERHUB_CREDS}", usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    bat 'echo %DOCKER_PASS%| docker login -u %DOCKER_USER% --password-stdin'
                    bat 'docker push %IMAGE_NAME%:%IMAGE_TAG%'
                    bat 'docker push %IMAGE_NAME%:latest'
                    bat 'docker logout'
                }
            }
        }

        stage('5. Deploy') {
            steps {
                powershell '''
                    if (-not (Test-Path .env) -and (Test-Path .env.example)) {
                        Copy-Item .env.example .env
                    }
                    $env:DOCKERHUB_IMAGE = "$env:IMAGE_NAME`:$env:IMAGE_TAG"
                    docker compose down --remove-orphans
                    docker compose up -d
                '''
            }
        }

        stage('6. Verify') {
            steps {
                powershell '''
                    Write-Host "Menunggu service AntriSehat siap..."
                    for ($i = 1; $i -le 12; $i++) {
                        try {
                            $res = Invoke-WebRequest -Uri "http://localhost:8000/health" -UseBasicParsing -TimeoutSec 5
                            if ($res.StatusCode -eq 200) {
                                Write-Host $res.Content
                                Write-Host "Verifikasi /health HTTP 200 OK berhasil!"
                                exit 0
                            }
                        } catch {}
                        Start-Sleep -Seconds 3
                    }
                    Write-Error "Verifikasi /health gagal setelah timeout."
                    exit 1
                '''
            }
        }
    }

    post {
        always {
            bat 'docker rmi antrisehat-test:%IMAGE_TAG% 2>nul || exit /b 0'
        }
    }
}
