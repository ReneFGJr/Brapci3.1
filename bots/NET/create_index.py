from sqlalchemy import (
    create_engine, MetaData, Table, Column,
    Integer, String, ForeignKey, Index
)
from sqlalchemy.orm import sessionmaker, declarative_base, relationship

DB_URI     = "mysql+pymysql://root:@localhost:3306/brapci_elastic?charset=utf8mb4"
IDX_DB_URI = "mysql+pymysql://root:@localhost:3306/brapci_elastic?charset=utf8mb4"

# engines e teste de conexão aqui…

Base = declarative_base()

class Record(Base):
    __tablename__ = "records"
    id     = Column(Integer, primary_key=True, autoincrement=True)
    src_id = Column(Integer, index=True, nullable=False)
    authors = relationship("Author", secondary="record_authors", back_populates="records")

class Author(Base):
    __tablename__ = "authors"
    id   = Column(Integer, primary_key=True, autoincrement=True)
    # agora 191 em vez de 255
    name = Column(String(191), unique=True, nullable=False, index=True)
    records = relationship("Record", secondary="record_authors", back_populates="authors")

class RecordAuthor(Base):
    __tablename__ = "record_authors"
    record_id = Column(Integer, ForeignKey("records.id"), primary_key=True)
    author_id = Column(Integer, ForeignKey("authors.id"), primary_key=True)

# recria o schema de índice
Base.metadata.drop_all(bind=create_engine(IDX_DB_URI))
Base.metadata.create_all(bind=create_engine(IDX_DB_URI))
