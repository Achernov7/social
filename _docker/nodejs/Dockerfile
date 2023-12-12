FROM node:alpine

WORKDIR /var/www
RUN chown -R root:root /var/www

COPY --chown=root:root package.json .

RUN npm install

COPY --chown=root:root . .

USER root

EXPOSE 5173

CMD ["sh", "-c", "npm run dev"]