#include "stdio.h"
#include "metis.h"

int getconfig(int * config);
int setoptions(METIS_OPTION opc, int * config);
int gettype();
int getsizes();
int getdata();

int mode;
int meshmode;

void main{
	int config[9];
	if(getconfig(config)==0)
		exit(0);
	METIS_OPTION opc *;
	if(setoptions(opc)==0)
		exit(0);
	if(opc[0]==0){
		
	}	
	else if(opc[0]==1){
		
	}		
	else exit(0);
	

	
}

int getconfig(int * config){
	FILE * fp;
	int i;
	fp = fopen("config.ini","r");
	if(fp==NULL)
		return 0;
	for(i=0;i<9;i++)
		fread(config[i],int,1,fp);
	fclose(fp);
	return 1;
}

int setoptions(METIS_OPTION opc, int * config){
	switch(config[1]){
		case 0:
			opc[METIS_OPTION_PTYPE]=METIS_PTYPE_KWAY;
		break;
		case 1:
			opc[METIS_OPTION_PTYPE]=METIS_PTYPE_RB;
		break;
		default:
			printf("PTYPE value is not valid\n");
			exit(0);
		break;
	}
	switch(config[2]){
		case 0:
			opc[METIS_OPTION_OBJTYPE]=METIS_OBJTYPE_CUT;
		break;
		case 1:
			opc[METIS_OPTION_OBJTYPE]=METIS_OBJTYPE_VOL;
		break;
		default:
			printf("OBJTYPE value is not valid\n");
			exit(0);
		break;
	}
	switch(config[3]){
		case 0:
			opc[METIS_OPTION_CTYPE]=METIS_CTYPE_RM;
		break;
		case 1:
			opc[METIS_OPTION_CTYPE]=METIS_CTYPE_SHEM;
		break;
		default:
			printf("CTYPE value is not valid\n");
			exit(0);
		break;
	}
	switch(config[4]){
		case 0:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_GROW;
		break;
		case 1:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_RANDOM;
		break;
		case 2:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_EDGE;
		break;
		case 3:
			opc[METIS_OPTION_IPTYPE]=METIS_IPTYPE_NODE;
		break;
		default:
			printf("IPTYPE value is not valid\n");
			exit(0);
		break;
	}
	switch(config[5]){
		case 0:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_FM;
		break;
		case 1:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_GREEDY;
		break;
		case 2:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_SEP2SIDED;
		break;
		case 3:
			opc[METIS_OPTION_RTYPE]=METIS_RTYPE_SEP1SIDED;
		break;
		default:
			printf("RTYPE value is not valid\n");
			exit(0);
		break;
	}
	switch(config[6]){
		case 0:
			opc[METIS_OPTION_CONTIG]=0;
		break;
		case 1:
			opc[METIS_OPTION_CONTIG]=1;
		break;
		default:
			printf("CONTIG value is not valid\n");
			exit(0);
		break;
	}
	if(config[7] == 0)
		opc[METIS_OPTION_UFACTOR] = 0;
	else if(config > 0)
 		opc[METIS_OPTION_UFACTOR] = config;
	else{
		printf("UFACTOR value is not valid\n");
		exit(0);
	}
	switch(config[0]){
		case 0:
			mode = 0;
		break:
		case 1:
			mode = 1;
			switch(config[8]){
				case 0: 
					meshmode = 0;
				break;
				case 1:
					meshmode = 1:
				break;
				default:
					printf("MESH MODE value is not valid\n");
					exit(0);
				break;
			}
			
		break;
		default:
			printf("MODE value is not valid\n");
			exit(0);
		break;
	}
}

int getsizes();
int getdata();
