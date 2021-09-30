FROM buildkite/puppeteer:latest
WORKDIR /app
COPY . /app

RUN wget --quiet https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -O /usr/sbin/wait-for-it.sh && chmod +x /usr/sbin/wait-for-it.sh

RUN npm install
ENV  PATH="${PATH}:/node_modules/.bin"
